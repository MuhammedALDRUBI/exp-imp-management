<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;

trait ImporterAbstractMethods
{ 
    /** 
     * @return string
     */
    abstract protected function getDataFileExpectedExtension(): string; 
    abstract protected function getDataFileContentProcessor() : DataFileContentProcessor;
}
