<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes;

use Exception;
use ExpImpManagement\DataFilesInfoManagers\ImportingDataFilesInfoManagers\ImportingRejectedDataFilesInfoManager;
use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\CSVFileContentProcessor;
use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\ImportingFilesProcessors\CSVImportingFilesProcessor;
use ExpImpManagement\ImportersManagement\Notifications\RejectedDataFileNotifier;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * @prop CSVImportingFilesProcessor $filesProcessor
 */
abstract class CSVImporter extends Importer
{
    protected array $dataToManuallyChange = [];
    protected ?string $rejectedDataFileName = null;
    protected ?string $rejectedDataFilePath = null;

    abstract public function getImportableFileFormatFactory() : CSVImportableFileFormatFactory;

    public function downloadFormat()
    {
        return $this->getImportableFileFormatFactory()->downloadFormat();
    }
    
    protected function getRejectedFileContent() : string
    {
        return $this->getImportableFileFormatFactory()->setDataFileToManuallyChange( $this->dataToManuallyChange )->getRawContent() ?? "";
    }

    protected function initFileProcessor() : Importer
    {
        if(!$this->filesProcessor)
        {
             $this->filesProcessor = new CSVImportingFilesProcessor(); 
        }
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

    protected function getDataFileContentProcessor() : DataFileContentProcessor
    {
        return new CSVFileContentProcessor();
    }

    protected function setModelDesiredColumns() : self
    {
        $formatFactory = $this->getImportableFileFormatFactory();
        if($formatFactory instanceof WithHeadings)
        {
            $this->ModelDesiredColumns = $formatFactory->headings();
            return $this;
        }
        return  parent::setModelDesiredColumns();
    }
  
    protected function addRejectedRowToManuallyChanging(array $row)
    {
        $this->dataToManuallyChange[] = $row;
    }

    protected function DoesItHaveRejectedRow() : bool
    {
        return !empty($this->dataToManuallyChange);
    }

    protected function failedModelImportingTransactrion(array $row , Exception $e) : void
    {
        parent::failedModelImportingTransactrion($row , $e);
        $this->addRejectedRowToManuallyChanging($row); 
    }
    

    protected function processRejectedDataFile($fileContent) : void
    {  
        $this->filesProcessor->HandleTempFileContentToCopy($fileContent , $this->rejectedDataFileName); 
        $this->filesProcessor->informImportingRejectedDataFilesInfoManager($this->rejectedDataFileName , $this->rejectedDataFilePath); 
    }

    protected function composeRejectedFilePath() : string
    {
        return $this->filesProcessor->getTempFileFolderPath($this->rejectedDataFileName)   ;
    }

    protected function composeRejectedDataFileName() : string
    { 
        return "importing-rejected-data-" . date("Y-m-d-h-i-s") . ".csv" ;
    }

    protected function generateFileAssetURL(string $fileName) : string
    {
        return URL::temporarySignedRoute(
                "rejected-data-file-downloading" ,
                      now()->addDays(ImportingRejectedDataFilesInfoManager::ValidityIntervalDayCount)->getTimestamp() ,
                     ["fileName" => $fileName]
                );
    }

    protected function importData() : Importer
    {
        parent::importData();

        /**
         * This is another format contains the rows have not stored in database .... this format also has DataValidation on each Cell 
         * it is used to allow user to know which rows aren't stored and chaging them manually
         */
        if($this->DoesItHaveRejectedRow() &&  $fileContent = $this->getRejectedFileContent() )
        {
            
            $this->rejectedDataFileName = $this->composeRejectedDataFileName();;
            $this->rejectedDataFilePath = $this->composeRejectedFilePath();

            //after this method the file will be copied to the temp path in the storage
            $this->processRejectedDataFile($fileContent);
  
            // nothing to do here ... the file already copied and the path will be passed to the convenient notification 
        }
        
        return $this;
    }

    public function getConvinientNotification() : Notification
    {
        if(!$this->rejectedDataFilePath)
        { 
            return parent::getConvinientNotification();
        }

        $fileAssetLink = $this->generateFileAssetURL($this->rejectedDataFileName) ; 
        return new RejectedDataFileNotifier($fileAssetLink);
    }
}
