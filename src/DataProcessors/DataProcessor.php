<?php

namespace ExpImpManagement\DataProcessors;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
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
        if($data instanceof EloquentCollection || $data instanceof LazyCollection)
        { 
            return $data->toArray();
        }

        if($data instanceof Collection )
        {
            $data = $data->all();
        }

        return $data;
    }
 
    protected function getProcessedDataRow(array $dataRow) : array
    {
        return $dataRow;
    }
    
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