<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use ExpImpManagement\ExportersManagement\Responders\JobDispatcherJSONResponder;
use ExpImpManagement\ExportersManagement\Responders\Responder;
use ExpImpManagement\ExportersManagement\Responders\StreamingResponder;

trait ResponderMethods
{
      
    protected function setJobDispatcherJSONResponderProps(JobDispatcherJSONResponder $responder )
    {
        $responder->setExporterClass($this);
    }
    protected function getJobDispatcherJSONResponder() : JobDispatcherJSONResponder
    {
        return new JobDispatcherJSONResponder();
    }

    /**
     * @return JobDispatcherJSONResponder
     * @throws JsonException
     */
    protected function initJobDispatcherJSONResponder() : JobDispatcherJSONResponder
    {
        $responder = $this->getJobDispatcherJSONResponder();
        $this->setResponderGeneralProps($responder);
        $this->setJobDispatcherJSONResponderProps($responder);
        return $responder; 
    }

    /**
     * Overwrite it on need in child class 
     */
    protected function setStreamingResponderProps(StreamingResponder $responder) : void
    {
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
        $this->setResponderGeneralProps($responder);
        $this->setStreamingResponderProps($responder);
        return  $responder; 
    }

    /**
     * Overwrite it on need in child class 
     */
    protected function setResponderGeneralProps(Responder $responder) : void
    {
        $responder->setDataCollectionToExport($this->DataCollection);
    }


}
