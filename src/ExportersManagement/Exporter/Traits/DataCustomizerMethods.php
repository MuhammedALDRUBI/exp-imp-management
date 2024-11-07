<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;


use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use Exception;
use ExpImpManagement\ExportersManagement\Interfaces\SupportSpatieAlowedFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as DatabaseQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\LazyCollection;
use Spatie\QueryBuilder\QueryBuilder;

trait DataCustomizerMethods
{ 
    protected string $ModelClass; 
    protected string $modelPrimaryKeyName;
    protected QueryBuilder | Builder | DatabaseQueryBuilder $builder;
    protected Collection | LazyCollection | null $DataCollection = null;
    protected int $LoadedRowsMaxLimitBeforeDispatchingJob = 5; 
    protected int $dataRowsCount; 
    protected ?Request $request = null; // needed to save request filters and payloads values to job object on needed to run job
 
    public function useRequest(Request $request) : self
    {
        $this->request = $request;
        return $this;
    }


    /**
     * @param Model $model
     * @return DataCustomizerMethods|Exporter
     */
    protected function setModelPrimaryKeyName(Model $model): self
    {
        $this->modelPrimaryKeyName = $model->getKeyName();
        return $this;
    }

    /**
     * @return DataCustomizerMethods|Exporter
     * @throws Exception
     */
    protected function setModelClass() : self
    { 
        $modelClass = $this->getModelClass();
        if(!class_exists($modelClass)){throw new Exception("The Given Model Class Is Undefined !");}

        $model = new $modelClass;
        if (!$model instanceof Model)
        {
            throw new Exception("The Given Model Class Is Not A Model Instance !");
        }
        $this->setModelPrimaryKeyName($model);
        unset($model);

        $this->ModelClass = $modelClass;

        return $this;
    }


    protected function applySpatieAllowedFilters() : void
    {
        if($this->builder instanceof QueryBuilder && $this instanceof SupportSpatieAlowedFilters)
        {
            $this->builder->allowedFilters( $this->getAllowedFilters() );
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
        return $this->ModelClass::newQuery();
    }
    protected function initSpatieBuilder() : QueryBuilder
    {
        return $this->getQueryBuilderClass()::for($this->ModelClass , $this->request);
    }
    /**
     * @return Builder | DatabaseQueryBuilder | QueryBuilder
     * @throws Exception
     * 
     * if another 
     */
    protected function initQueryBuilder() : Builder | DatabaseQueryBuilder | QueryBuilder
    {
        $this->setModelClass();

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

        if(empty($this->getWithRelationshipsArray()))
        {
            $this->cursorData();
            return $this;
        }

        $this->LazyDataById();
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
