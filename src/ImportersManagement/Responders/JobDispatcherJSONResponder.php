<?php

namespace ExpImpManagement\ImportersManagement\Responders;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Jobs\DataImporterJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class JobDispatcherJSONResponder  extends Responder
{
    protected ?Importer $importer = null;

    /**
     * @return $this
     * @throws Exception
     */
    protected function initJob() : DataImporterJob
    {
        
        if(!$this->importer)
        {
            throw new Exception("There Is No Importer Passed To Job Object");
        }
        
        return new DataImporterJob($this->importer);
    }

    public function setImporter(Importer $importer): self
    {
        $this->importer = $importer; 
        return $this;
    }
    
    /**
     * @return JsonResponse
     */
    public function respond(): JsonResponse
    {
        
        $job = $this->initJob();
        dispatch($job);
        return Response::success([] , ["Your Data File Has Been Uploaded Successfully ! , You Will Receive Your Request Result By Mail Message On Your Email !"]);
    }

}