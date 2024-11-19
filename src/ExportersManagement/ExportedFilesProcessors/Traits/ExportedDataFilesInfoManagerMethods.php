<?php

namespace ExpImpManagement\ExportersManagement\ExportedFilesProcessors\Traits;

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use CustomFileSystem\CustomFileHandler;

trait ExportedDataFilesInfoManagerMethods
{

    protected ?ExportedDataFilesInfoManager $exportedDataFilesInfoManager = null;

    protected function initExportedDataFilesInfoManager() : self
    {
        if(! $this->exportedDataFilesInfoManager)
        {
            $this->exportedDataFilesInfoManager = new ExportedDataFilesInfoManager();
        }
        
        return $this;
    }

    protected function informExportedDataFilesInfoManager(string $fileRelevantPath) : string
    {
        /**
         * $fileRelevantPath comming after uploading a file to the temp folder path ...it contains the tem folder names
         */
        $this->initExportedDataFilesInfoManager();

        $fileName = $this->getFileDefaultName($fileRelevantPath); 

        $fileRealPath = CustomFileHandler::getFileStoragePath($fileRelevantPath , $this->tempFilesDisk);

        return $this->exportedDataFilesInfoManager->addNewFileInfo( $fileName , $fileRealPath , $fileRelevantPath )
                                                  ->SaveChanges();
    }
}
