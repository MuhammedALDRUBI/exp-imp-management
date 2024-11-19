<?php

namespace ExpImpManagement\ImportersManagement\Responders;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Jobs\DataImporterJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class JobDispatcherJSONResponder  extends Responder
{
    protected ?string $importerClass = null;
    protected ?string $uploadedFileTempRealPath = null; 
    protected ?DataImporterJob $job = null;

    /**
     * @return $this
     * @throws Exception
     */
    protected function initJob() : self
    {
        if($this->job)
        {
            return $this;
        }
        if(!$this->importerClass)
        {
            throw new Exception("There Is No Importer Class Given To Job Object");
        }

        if(!$this->uploadedFileTempRealPath)
        {
             throw new Exception("The Imported File Path Is Not Passed To JobDispatcherJSONResponder");
        }

        $this->job = new DataImporterJob($this->importerClass );
        return $this;
    }

    /**
     * @param Importer $importer
     * @return $this
     * @throws Exception
     */
    public function setImporterClass(Importer $importer): self
    {
        $this->importerClass = get_class($importer); 
        return $this;
    }

    /**
     * @param string $importedDataFileStoragePath
     * @return $this
     * @throws Exception
     */
    public function setImportedDataFileTempPath(string $uploadedFileTempRealPath  ): self
    {
        $this->uploadedFileTempRealPath = $uploadedFileTempRealPath;
        return $this;
    }
 
    /**
     * @return JsonResponse
     */
    public function respond(): JsonResponse
    {
        
        $this->initJob();
        $this->job->setImportedDataFileTempPath($this->uploadedFileTempRealPath);
        dispatch($this->job);
        return Response::success([] , ["Your Data File Has Been Uploaded Successfully ! , You Will Receive Your Request Result By Mail Message On Your Email !"]);
    }

}
