<?php

namespace ExpImpManagement\ImportersManagement\ImportingFilesProcessors;

use CustomFileSystem\CustomFileHandler;
use ExpImpManagement\DataFilesInfoManagers\ImportingDataFilesInfoManagers\ImportingRejectedDataFilesInfoManager;

class CSVImportingFilesProcessor extends ImportingFilesProcessor
{

    protected ?ImportingRejectedDataFilesInfoManager $importingRejectedDataFilesInfoManager = null;

    protected function initImportingRejectedDataFilesInfoManager() : self
    {
        if(! $this->importingRejectedDataFilesInfoManager)
        {
            $this->importingRejectedDataFilesInfoManager = new ImportingRejectedDataFilesInfoManager();
        }
        
        return $this;
    }

    public function informImportingRejectedDataFilesInfoManager(string $fileRelevantPath) : string
    {
        $this->initImportingRejectedDataFilesInfoManager();

        $fileName = $this->getFileDefaultName($fileRelevantPath);

        $fileRealPath = CustomFileHandler::getFileStoragePath($fileRelevantPath , $this->tempFilesDisk);

        return $this->importingRejectedDataFilesInfoManager->addNewFileInfo( $fileName , $fileRealPath , $fileRelevantPath )
                                                           ->SaveChanges();
    }
}