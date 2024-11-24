<?php

namespace ExpImpManagement\ExportersManagement\ExportedFilesProcessors;


use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\Traits\ExportedDataFilesInfoManagerMethods;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;
use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;

class ExportedFilesProcessor extends TemporaryFilesProcessor
{
    protected string $TempFilesFolderName = "tempFiles/ExportedTempFiles";

    use  ExportedDataFilesInfoManagerMethods;

    protected ?ExportedDataFilesInfoManager $exportedDataFilesInfoManager = null;

    protected function initExportedDataFilesInfoManager() : self
    {
        if(! $this->exportedDataFilesInfoManager)
        {
            $this->exportedDataFilesInfoManager = new ExportedDataFilesInfoManager();
        }
        
        return $this;
    }

    public function informExportedDataFilesInfoManager(string $fileRealPath) : string
    {
        /**
         * $fileRelevantPath comming after uploading a file to the temp folder path ...it contains the tem folder names
         */
        $this->initExportedDataFilesInfoManager();

        $fileName = $this->getFileDefaultName($fileRealPath); 

        $fileRelevantPath = $this->getTempFileRelevantPath($fileName) ;

        return $this->exportedDataFilesInfoManager->addNewFileInfo( $fileName , $fileRealPath , $fileRelevantPath )
                                                  ->SaveChanges();
    }
 
}
