<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;

trait ExporterAbstractMethods
{ 
    /**
     * Must be defined in the end concrete child class
     */
    abstract protected function getModelClass() : string;
    abstract protected function getDocumentTitle() : string;

    
    /**
     * Generally will be defined in the child abstract classes
     */
    abstract protected function getDataFileExtension() : string;
    abstract protected function getStreamingResponder() : StreamingResponder;
    abstract protected function setDataFileToExportedFilesProcessor() : string;
}
