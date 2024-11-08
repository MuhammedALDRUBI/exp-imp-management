<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;

trait ImporterAbstractMethods
{
    abstract protected function getModelClass() : string;
    abstract protected function getDataValidationRequestForm() : string;


    /** 
     * @return string
     */
    abstract protected function getDataFileExpectedExtension(): string; 
    abstract protected function getDataFileContentProcessor() : DataFileContentProcessor;
}
