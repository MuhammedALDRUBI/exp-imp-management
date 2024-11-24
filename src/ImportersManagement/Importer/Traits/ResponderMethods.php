<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\Responders\JobDispatcherJSONResponder;
use ExpImpManagement\ImportersManagement\Responders\Responder;
use Exception;

trait ResponderMethods
{ 
    protected function getJobDispatcherResponder() :  JobDispatcherJSONResponder
    {
        return new JobDispatcherJSONResponder();
    }

    /**
     * @throws JsonException
     */
    protected function setResponderProps(JobDispatcherJSONResponder $responder) : void
    {
        $responder->setImporter($this); 
    }
    /**
     * @throws Exception
     */
    protected function initResponder() : Responder
    { 
            $responder = $this->getJobDispatcherResponder();

            $this->setResponderProps($responder);

            return $responder; 
    }
}
