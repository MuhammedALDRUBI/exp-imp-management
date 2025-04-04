<?php

namespace ExpImpManagement\ImportersManagement\Importer;

use ExpImpManagement\ImportersManagement\Importer\Traits\DataCustomizerMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\UploadedFileOperations;
use ExpImpManagement\ImportersManagement\Importer\Traits\ImporterAbstractMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\ResponderMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\ValidationMethods;
use Exception;
use ExpImpManagement\ImportersManagement\DataFilesContentExtractors\DataFilesContentExtractor;
use ExpImpManagement\ImportersManagement\Importer\Traits\DataFilesContentExtractorMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\DataImportingHooks;
use ExpImpManagement\ImportersManagement\Importer\Traits\ImportableDataProcessorMethods;
use ExpImpManagement\ImportersManagement\Importer\Traits\ImporterSerilizing;
use ExpImpManagement\ImportersManagement\Importer\Traits\NotificationMethods;
use ExpImpManagement\ImportersManagement\ImportingFilesProcessors\ImportingFilesProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use JsonSerializable;

abstract class Importer implements JsonSerializable
{

    use ValidationMethods , UploadedFileOperations , DataCustomizerMethods , DataImportingHooks ,
        ResponderMethods , ImporterAbstractMethods   , NotificationMethods , ImporterSerilizing ,
        ImportableDataProcessorMethods , DataFilesContentExtractorMethods;

    
    protected array $ImportedDataArray = [];
    protected bool $truncateTableBeforeImproting = false;
    protected ?string $ModelClass = null; 
    protected ?ImportingFilesProcessor $filesProcessor = null;

    public function __construct(string $ModelClass , string $dataValidationRequestFormClass )
    {
        $this->setModelClass($ModelClass)->setDataValidationRequestFormClass($dataValidationRequestFormClass);
    }

    protected function initFileProcessor() : Importer
    {
        if(!$this->filesProcessor)
        {
             $this->filesProcessor = new ImportingFilesProcessor(); 
        }
        return $this;
    }
 
    protected function finishImportingOperation() : Importer
    {
        return $this->deleteTempUploadedFile();
    }
 
    /**
     * @return Collection
     * @throws Exception
     */
    protected function getFileDataCollection() : Collection
    {
        $this->openImportedDataFileForProcessing();
        return $this->extractFileDataToImport();
    }

    /**
     * @return Importer
     * @throws Exception
     */
    protected function fetchFileData() : Importer
    {
        $fileData = $this->getFileDataCollection();
        $this->ImportedDataArray = $this->processFileData($fileData)->toArray();
        return $this;
    }

    /**
     * @return void
     */
    public function importingJobFun() : void
    {
        $this->setupImporter()->fetchFileData()->importData()->finishImportingOperation();
    }

    protected function setupImporter() : Importer
    {
        return $this->initFileProcessor()->setValidationManager(); 
    }

    /**
     *  @throws Exception
     * @return JsonResponse
     */
    public function import() : JsonResponse
    {
        try{
            $this->setupImporter()->HandleUploadedFile()->setTableTruncatingStatus();
            return $this->initResponder()->respond();
        }catch(Exception $e)
        {
            return Response::error([$e->getMessage()]);
        }
    }

}
