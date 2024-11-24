<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits;

 
use ExpImpManagement\DataFilesInfoManagers\ImportingDataFilesInfoManagers\ImportingRejectedDataFilesInfoManager;
use ExpImpManagement\ImportersManagement\Notifications\RejectedDataFileNotifier;
use Illuminate\Support\Facades\URL; 
use Illuminate\Notifications\Notification;

trait DataImportingFailingHandling
{
    use DataImportHookExtension;

    protected ?string $rejectedDataFileName = null;
    protected ?string $rejectedDataFilePath = null;
    protected array $dataToManuallyChange = [];

    protected function addRejectedRowToManuallyChanging(array $row)
    {
        $this->dataToManuallyChange[] = $row;
    }

    protected function DoesItHaveRejectedRow() : bool
    {
        return !empty($this->dataToManuallyChange);
    }

    protected function getRejectedFileContent() : string
    {
        return $this->getImportableFileFormatFactory()->setDataFileToManuallyChange( $this->dataToManuallyChange )->getRawContent() ?? "";
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

    protected function setRejectedDataFilePath() : void
    {
        $this->rejectedDataFilePath = $this->composeRejectedFilePath();
    }
    
    protected function composeRejectedDataFileName() : string
    { 
        return "importing-rejected-data-" . date("Y-m-d-h-i-s") . ".csv" ;
    }

    protected function setRejectedDataFileName() : self
    {
        $this->rejectedDataFileName = $this->composeRejectedDataFileName();
        return $this;
    }

    protected function generateRejectedDataFileAssetURL(string $fileName) : string
    {
        return URL::temporarySignedRoute(
                                            "rejected-data-file-downloading" ,
                                            now()->addDays(ImportingRejectedDataFilesInfoManager::ValidityIntervalDayCount)->getTimestamp() ,
                                            ["fileName" => $fileName]
                                        );
    }

    protected function getRejectedDataFileNotification() : Notification
    {  
        $fileAssetLink = $this->generateRejectedDataFileAssetURL($this->rejectedDataFileName) ; 
        return new RejectedDataFileNotifier($fileAssetLink);
    }
}