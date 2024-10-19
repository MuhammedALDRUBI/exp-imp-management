<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\Responders\JobDispatcherJSONResponder;
use ExpImpManagement\ImportersManagement\Responders\Responder;
use Exception;

trait ResponderMethods
{

    protected ?Responder $responder = null;

    protected function getJobDispatcherResponder() :  JobDispatcherJSONResponder
    {
        return new JobDispatcherJSONResponder();
    }

    /**
     * @throws JsonException
     */
    protected function setResponderProps() : Responder
    {
        return $this->responder->setImporterClass($this)
                               ->setImportedDataFileStoragePath($this->uploadedFileStorageRelevantPath );
    }
    /**
     * @throws Exception
     */
    protected function initResponder() : Responder
    {
        if(!$this->responder){$this->responder = $this->getJobDispatcherResponder();}
        return $this->setResponderProps();
    }
}
