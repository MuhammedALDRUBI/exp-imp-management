<?php

namespace ExpImpManagement\ImportersManagement\Importer;

use ExpImpManagement\ImportersManagement\Importer\Traits\DataCustomizerMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\FilesImportingMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\UploadedFileOperations;
use ExpImpManagement\ImportersManagement\Importer\Traits\ImporterAbstractMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\ResponderMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\ValidationMethods;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

abstract class Importer
{

    use ValidationMethods , UploadedFileOperations , DataCustomizerMethods ,
        ResponderMethods , ImporterAbstractMethods  ;

    const ImportedUploadedFilesTempFolderName =  "ImportedDataTempFiles";
    protected string $uploadedFileTempRealPath;  //temp file real path (not in storage ... it is in the temp path for manipulating and deleting after process is done)
    protected array $ImportedDataArray = [];
    protected ?string $ModelClass = null;

    protected ?TemporaryFilesProcessor $filesProcessor = null;

    protected function initFileProcessor() : Importer
    {
        if(!$this->filesProcessor)
        {
             $this->filesProcessor = new TemporaryFilesProcessor(); 
        }
        return $this;
    }
 
    /**
     * @return $this
     * @throws Exception
     */
    protected function setFileDataArray() : self
    {
        $this->openImportedDataFileForProcessing();
        $this->ImportedDataArray = $this->getDataToImport(); 
        return $this;
    }

    /**
     * @return Importer
     * @throws Exception
     */
    protected function fetchFileData() : Importer
    {
        return $this->setFileDataArray();
    }

    protected function successfulImportingTransaction() : Importer
    {
        DB::commit();
        return $this->deleteTempUploadedFile();
    }

    protected function failedImportingTransactrion() : Importer
    {
        DB::rollBack();
        return $this;
    }
    protected function startImportingDBTransaction() : void
    {
        DB::beginTransaction();
    }

    /**
     * @return void
     */
    public function importingJobFun() : void
    {
        try {
            $this->setupImporter()->fetchFileData()->importData();
            $this->successfulImportingTransaction();
        }catch (Exception $e)
        {
            $this->failedImportingTransactrion();
        }
    }
    protected function setupImporter() : Importer
    {
        return $this->initFileProcessor()->setModelClass()->setValidationManger();
    }

    /**
     *  @throws Exception
     * @return JsonResponse
     */
    public function import() : JsonResponse
    {
        try{
            $this->setupImporter()->HandleUploadedFile();
            return $this->initResponder()->respond();
        }catch(Exception $e)
        {
            return Response::error([$e->getMessage()]);
        }
    }

}
