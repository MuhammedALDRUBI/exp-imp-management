<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;

trait ExporterAbstractMethods
{ 
    abstract protected function getModelClass() : string;
    abstract protected function getDataFileExtension() : string;
    abstract protected function getDocumentTitle() : string;

    abstract protected function getStreamingResponder() : StreamingResponder;
    abstract protected function setDataFileToExportedFilesProcessor() : string;
}
