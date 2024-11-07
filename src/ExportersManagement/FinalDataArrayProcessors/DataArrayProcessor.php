<?php

namespace ExpImpManagement\ExportersManagement\FinalDataArrayProcessors;

use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\Traits\DataArrayMappingMethodsTrait; 
use Exception;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\Traits\ObjectValueHandlers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class DataArrayProcessor
{
    use DataArrayMappingMethodsTrait , ObjectValueHandlers ;

    protected array $ModelDesiredFinalColumns = ['*'];
    protected Collection | LazyCollection | null $DataCollection = null;

    /**
     * @param Collection|LazyCollection $DataCollection
     * @return $this
     */
    public function setDataCollection(Collection|LazyCollection $DataCollection): self
    {
        $this->DataCollection = $DataCollection;
        return $this;
    }

    protected function getModelDesiredColumnOnDataFound() : array
    {
        $model = $this->getDataCollection()->first();
        return  $this->getModelOrCollectionAttributesKeysArray($model);
    }
    protected function getModelDesiredColumnsIfNoData() : array
    {
        return [];
    }
   /**
     * @return DataArrayProcessor|ModelDesiredColumnsValidator
     */
    protected function setModelDesiredFinalDefaultColumns( ) : self
    {
        if( Arr::first($this->ModelDesiredFinalColumns) == '*')
        {
            //There Is No Need To Check If DataCollection Has Data Or Not
            //Because That Always Has Data Items If Execution Arrived at This Point
            /** @var Model $model */
            if($this->getDataCollection()->isEmpty())
            {
                //nothing will be selected
                $this->ModelDesiredFinalColumns = $this->getModelDesiredColumnsIfNoData();
                return  $this;
            }

            $this->ModelDesiredFinalColumns = $this->getModelDesiredColumnOnDataFound();
        }

        return  $this;
    }

    /**
     * @param array $ModelDesiredFinalColumns
     * @return $this
     */
    public function setModelDesiredFinalDefaultColumnsArray(array $ModelDesiredFinalColumns = []): self
    {
        $this->ModelDesiredFinalColumns = $ModelDesiredFinalColumns;
        return $this;
    }

    protected function processModelSingleDesiredColumns(string $column , Model $model ,array $row = []) : array
    {
        $row[ $column ] =  $this->getObjectKeyValue($column, $model );
        return $row;
    }
    /**
     * @param Model $model
     * @param array $row
     * @return array
     */
    protected function processModelDesiredColumns(Model $model ,array $row = []) : array
    {
        foreach ($this->ModelDesiredFinalColumns as $column)
        {
            $row = $this->processModelSingleDesiredColumns($column, $model , $row );
        }
        return $row;
    }

    protected function processDataRow(Model $model) : array
    {
        return  $this->processModelDesiredColumns($model);
    }
    /**
     * @return Collection|LazyCollection|null
     */
    public function getDataCollection(): Collection|LazyCollection|null
    {
        return $this->DataCollection;
    }

    protected function DataSetup(Collection | LazyCollection $collection ) : self
    {
        return $this->setDataCollection($collection)->setModelDesiredFinalDefaultColumns();
    }

    /**
     * @param Collection|LazyCollection $collection
     * @return Collection
     * @throws Exception
     */
    public function getProcessedData(Collection | LazyCollection $collection  ): Collection
    {
        $this->DataSetup($collection);

        $finalData = [];
        foreach ($this->getDataCollection() as $model)
        {
            $row = $this->processDataRow($model);
            if(!empty($row))
            {
                 $finalData[] = $row; 
            }
        }
        $finalData = $this->callMappingFunOnRowsArray($finalData);
        return collect($finalData);
    }

}
