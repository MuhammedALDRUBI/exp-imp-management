<?php

namespace ExpImpManagement\DataProcessors\ExportableDataProcessors;

use ExpImpManagement\DataProcessors\DataProcessor;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class ExportableDataProcessor extends DataProcessor
{ 

      /**
     * The default behavior is to return the same data ... it can be overriten by a child class
     */
    public function processData(LazyCollection | Collection | array $collection) : LazyCollection | Collection
    {
        return $collection;
    }


}