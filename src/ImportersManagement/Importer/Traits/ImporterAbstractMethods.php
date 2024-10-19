<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\Importer\DataFilesContentProcessors\DataFileContentProcessor;

trait ImporterAbstractMethods
{
    abstract protected function getModelClass() : string;
    abstract protected function getDataFileContentProcessor() : DataFileContentProcessor;
}
