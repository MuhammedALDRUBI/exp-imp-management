<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;

trait ImporterAbstractMethods
{
    abstract protected function getModelClass() : string;
    abstract protected function getDataFileContentProcessor() : DataFileContentProcessor;
    
    /** 
     * @return string
     */
    abstract protected function getDataFileExpectedExtension(): string; 
}
