<?php

namespace ExpImpManagement\DataProcessors;


use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

abstract class DataProcessor
{
    protected function convertToCollection(array $data) : Collection
    {
        return collect($data);
    }
    protected function convertToArray(LazyCollection | Collection | array $data)
    {
        if($data instanceof Collection || $data instanceof LazyCollection)
        {
            $data = $data->all();
        }
        return $data;
    }
    //abstract public function processData(LazyCollection | Collection | array $collection) : LazyCollection | Collection;
    abstract protected function getProcessedDataRow(array $dataRow) : array;
    
    public function processData(LazyCollection | Collection | array $data) : LazyCollection | Collection
    {
        $finalData = [];
 
        foreach($this->convertToArray($data) as $dataRow)
        {
           $finalData[] = $this->getProcessedDataRow($dataRow);
        }
        return $this->convertToCollection($finalData);
    }
}