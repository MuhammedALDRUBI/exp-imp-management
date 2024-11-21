<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;

trait ExporterAbstractMethods
{   
    /**
     * Generally will be defined in the child abstract classes
     */
    abstract protected function getDataFileExtension() : string;
    abstract protected function getStreamingResponder() : StreamingResponder;
    abstract protected function uploadDataFileToTempPath() : string;
}
