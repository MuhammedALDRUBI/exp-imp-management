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
    protected function setResponderProps(JobDispatcherJSONResponder $responder) : void
    {
        $responder->setImporterClass($this)
                  ->setImportedDataFileStoragePath($this->uploadedFileStorageRelevantPath );
    }
    /**
     * @throws Exception
     */
    protected function initResponder() : Responder
    {
        if(!$this->responder)
        {
            $responder = $this->getJobDispatcherResponder();

            $this->setResponderProps($responder);

            $this->responder = $responder;
        }
        return $this->responder;
    }
}
