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
        ResponderMethods , ImporterAbstractMethods  , FilesImportingMethods;

    const ImportedUploadedFilesTempFolderName =  "ImportedDataTempFiles";
    protected string $ExtractedUploadedFileTempRealPath; /** Folder Contains Data File and Files Must Be Imported */
    protected array $ImportedDataArray = [];

    protected ?TemporaryFilesProcessor $filesProcessor = null;

    protected function initFileProcessor() : Importer
    {
        if(!$this->filesProcessor){ $this->filesProcessor = new TemporaryFilesProcessor(); }
        return $this;
    }

    /**
     * Will Be Overridden In Child Classes (Based On Type)
     * @return string
     */
    protected function getDataFileExpectedExtension(): string
    {
        return "csv";
    }

    protected function getDataFilePath() : string
    {
        return $this->ExtractedUploadedFileTempRealPath . "/"
            . $this->filesProcessor->getFileDefaultName($this->ExtractedUploadedFileTempRealPath , "" , false)
            . "." . $this->getDataFileExpectedExtension();
    }
    /**
     * @return $this
     * @throws Exception
     */
    protected function setFileDataArray() : self
    {
        $this->openImportedDataFileForProcessing();
        $this->ImportedDataArray = $this->getDataToImport();
        if(empty($this->ImportedDataArray)){$this->throwEmptyDataException() ;}
        return $this;
    }

    /**
     * @return Importer
     * @throws Exception
     */
    protected function fetchFileData() : Importer
    {
        return $this->setFileDataArray()->validateFileData();
    }

    protected function successfulImporting() : Importer
    {
        DB::commit();
        return $this->deleteTempUploadedFile();
    }

    protected function failedImporting() : Importer
    {
        DB::rollBack();
        return $this->deleteImportedFiles();
    }

    /**
     * @return void
     */
    public function importingJobFun() : void
    {
        try {
            $this->setupImporter()->fetchFileData()->importData()->importFiles();
            $this->successfulImporting();
        }catch (Exception $e)
        {
            $this->failedImporting();
        }
    }
    protected function setupImporter() : Importer
    {
        return $this->initFileProcessor();
    }

    /**
     *  @throws Exception
     * @return JsonResponse
     */
    public function import() : JsonResponse
    {
        try{
            $this->setupImporter()->HandleUploadedFile();
            return $this->initResponder()->informDeleteToImportedDataFileAfterProcessing($this->DeleteUploadedFileAfterProcessing)->respond();
        }catch(Exception $e)
        {
            return Response::error([$e->getMessage()]);
        }
    }

}
