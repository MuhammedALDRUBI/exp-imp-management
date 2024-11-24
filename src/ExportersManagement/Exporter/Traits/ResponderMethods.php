<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use ExpImpManagement\ExportersManagement\Responders\JobDispatcherJSONResponder;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;

trait ResponderMethods
{
   
    /**
     * @return JobDispatcherJSONResponder
     * @throws JsonException
     */
    protected function initJobDispatcherJSONResponder() : JobDispatcherJSONResponder
    {
        return new JobDispatcherJSONResponder($this); 
    }

    /**
     * Overwrite it on need in child class 
     */
    protected function setStreamingResponderProps(StreamingResponder $responder) : void
    {
        $responder->setDataCollectionToExport($this->DataCollection);
        $responder->setFileFullName($this->fileFullName);;
    }

    /**
     * @return JobDispatcherJSONResponder
     * @throws JsonException
     */
    protected function initStreamingResponder() : StreamingResponder
    {
        $this->PrepareExporterData();
        $responder = $this->getStreamingResponder(); 
        $this->setStreamingResponderProps($responder);
        return  $responder; 
    }
 
}
