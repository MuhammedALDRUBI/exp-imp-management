<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\DataProcessors\ImportableDataProcessors\ImportableDataProcessor;
use ExpImpManagement\ImportersManagement\DataFilesContentExtractors\DataFilesContentExtractor; 
use Illuminate\Database\Eloquent\Model;

trait ImporterAbstractMethods
{ 
    /** 
     * @return string
     */
    abstract protected function getDataFileExpectedExtension(): string; 
    abstract protected function getDataFilesContentExtractor() : DataFilesContentExtractor;
    abstract protected function getDefaultImportableDataProcessor() : ImportableDataProcessor;
    abstract protected function getCurrentModelFillableValues(array $row) : array;
    abstract protected function handleModelRelationships(Model $model) : void;
}
