<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;


use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as DatabaseQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\LazyCollection;
use Spatie\QueryBuilder\QueryBuilder;

trait DataCustomizerMethods
{ 
    protected ?string $ModelClass  = null;
    protected string $modelPrimaryKeyName;
    protected QueryBuilder | Builder | DatabaseQueryBuilder | null $builder = null; 
    protected Collection | LazyCollection | null $DataCollection = null;
    protected int $LoadedRowsMaxLimitBeforeDispatchingJob = 5; 
    protected int $dataRowsCount; 
    protected ?Request $request = null; // needed to save request filters and payloads values to job object on needed to run job
    protected array $spatieBuilderAllowedFilters = [];
 
    public function useRequest(Request $request) : self
    {
        $this->request = $request;
        return $this;
    }
 
    public function getSpatieBuilderAllowedFilters() : array
    {
        return $this->spatieBuilderAllowedFilters ;
    }

    public function setSpatieBuilderAllowedFilters(array $allowedFilters) : self
    {
       return $this->spatieBuilderAllowedFilters = $allowedFilters;
    }

    /**
     * @param Model $model
     * @return DataCustomizerMethods|Exporter
     */
    protected function setModelPrimaryKeyName($modelClass): self
    {
        $model = new $modelClass;
        $this->modelPrimaryKeyName = $model->getKeyName();
        unset($model);
        return $this;
    }

    /**
     * @return DataCustomizerMethods|Exporter
     * @throws Exception
     */
    public function setModelClass(string $modelClass) : self
    {   
        if(!is_subclass_of($modelClass , Model::class))
        {
            throw new Exception("The passed Model class is not a model type !");
        } 
        
        $this->setModelPrimaryKeyName($modelClass);

        $this->ModelClass = $modelClass;

        return $this;
    }

    protected function setModelClassOptinally(?string $modelClass = null)
    {
        if($modelClass)
        {
            $this->setModelClass($modelClass);
        }
    }

    public function getModelClass() : ?string
    {
        return $this->ModelClass;
    }

    protected  function requireModelClass() : string
    {
        return $this->getModelClass() 
               ?? 
               throw new Exception("The model class is not set while it is required to fetch data");
    }
  
    protected function applySpatieAllowedFilters() : void
    {
        if($this->builder instanceof QueryBuilder && !empty( $this->getSpatieBuilderAllowedFilters() ))
        {
            $this->builder->allowedFilters( $this->getSpatieBuilderAllowedFilters() );
        }
    }

    protected function getPixelDefaultScopes() : array
    {
        return ['datesFiltering' , 'customOrdering'];
    }
 
    /**
     * If more advanced functinality is needed ... override the two functions applyPixelDefaultScopes , getPixelDefaultScopes
     * because we are preparing the default query builder only
     */
    protected function applyPixelDefaultScopes() : void
    {
        foreach($this->getPixelDefaultScopes() as $scope)
        {
            $this->builder->{$scope}();
        }
    }

    protected function callOnBuilder() : void
    {
        $this->applyPixelDefaultScopes();
        $this->applySpatieAllowedFilters();
    }

    public function useQueryBuilder(Builder | DatabaseQueryBuilder | QueryBuilder $builder) : self
    {
        $this->builder = $builder;
        return $this;
    }
    
    protected function getQueryBuilderClass() : string
    {
        return QueryBuilder::class;
    }

    protected function initEloquentBuilder() : Builder
    {
        $modelClass = $this->requireModelClass(); 
        return $modelClass::query();
    }
    protected function initSpatieBuilder() : QueryBuilder
    {
        return $this->getQueryBuilderClass()::for($this->requireModelClass() , $this->request);
    }

    /**
     * @return Builder | DatabaseQueryBuilder | QueryBuilder
     * @throws Exception
     *  
     */
    protected function initQueryBuilder() : Builder | DatabaseQueryBuilder | QueryBuilder
    { 
        if(is_subclass_of($this->getQueryBuilderClass() , QueryBuilder::class))
        {
            return  $this->initSpatieBuilder();
        } 
        return $this->initEloquentBuilder();
    }

    protected function prepareQueryBuilder() : void
    {
        if(!$this->builder)
        {
            $builder = $this->initQueryBuilder();
            $this->useQueryBuilder($builder);
            $this->callOnBuilder();
        }
    }
    /**
     * @param int $count
     * @return DataCustomizerMethods|Exporter
     * @throws Exception
     */
    protected function setNeededDataCount(?int $count = null) : self
    {
        if(!$count)
        {
            $count =  $this->builder->count();
        }

        if($count == 0 ) 
        {
             throw $this->getEmptyDataException();
        }

        $this->dataRowsCount = $count;
        return $this;
    }

    protected function LazyDataById() : void
    {
        $this->DataCollection = $this->builder->lazyById($this->LoadedRowsMaxLimitBeforeDispatchingJob , $this->modelPrimaryKeyName);
    }

    protected function cursorData() : void
    {
        $this->DataCollection = $this->builder->cursor();
    }

    /**
     * @param Collection|LazyCollection|null  $collection
     * @return DataCustomizerMethods|Exporter
     */
    protected function setDataCollection(Collection|LazyCollection|null $collection = null) : self
    {
        if($collection != null)
        {
            $this->DataCollection = $collection;
            return $this;
        }

        if($this->dataRowsCount > $this->LoadedRowsMaxLimitBeforeDispatchingJob)
        {
            $this->LazyDataById();
            return $this;
        }
 
        $this->cursorData();
        return $this;  
    }

    /**
     * @return DataCustomizerMethods|Exporter
     */
    protected function setDefaultDataCollection() : self
    {
        if(!$this->DataCollection)
        {
            $this->setDataCollection();
        }
        return $this;
    }

    /**
     * @param Collection|LazyCollection $DataCollection
     * @return DataCustomizerMethods|Exporter
     * @throws Exception
     * This Method is used to change Exported Data from controller context ... but it is mainly changed
     * by setDefaultData method in the constructor of object
     */
    public function useDataCollection( Collection | LazyCollection|null $DataCollection = null ) : self
    { 
        if($DataCollection)
        {
            $this->setNeededDataCount($DataCollection->count())->setDataCollection($DataCollection);
        }
        return $this;
    }

    protected function getEmptyDataException() : Exception
    {
        return new  Exception("Data Array Or Collection Can't Be Empty !") ;
    }

    protected function DoesHaveBigData() : bool
    { 
        return $this->dataRowsCount > $this->LoadedRowsMaxLimitBeforeDispatchingJob;
    }
 
}
