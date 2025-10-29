<?php

namespace ExpImpManagement\ExportersManagement\ExportedFilesProcessors;

use Illuminate\Console\Scheduling\Schedule;
use CustomFileSystem\CustomFileHandler;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;
use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\Jobs\OldDataExportersDeleterJob;

class ExportedFilesProcessor extends TemporaryFilesProcessor
{
    protected string $TempFilesFolderName = "tempFiles/ExportedTempFiles";

    protected ?ExportedDataFilesInfoManager $exportedDataFilesInfoManager = null;

    protected function initExportedDataFilesInfoManager() : self
    {
        if(! $this->exportedDataFilesInfoManager)
        {
            $this->exportedDataFilesInfoManager = new ExportedDataFilesInfoManager();
        }
        
        return $this;
    }

    public function informFilesInfoManagerUsingRealPath(string $fileRealPath) : string
    {
        
        $this->initExportedDataFilesInfoManager();

        $fileName = $this->getFileDefaultName($fileRealPath); 
        
        /**
         * $fileRelevantPath comming after uploading a file to the temp folder path 
         * It contains the tem folder names
         */

        $fileRelevantPath = $this->getTempFileRelevantPath($fileName) ;

        return $this->exportedDataFilesInfoManager->addNewFileInfo( $fileName , $fileRealPath , $fileRelevantPath )
                                                  ->SaveChanges();
    }

    protected function informFilesInfoManagerUsingRelaventPath(string $fileRelevantPath) : string
    {
        /**
         * $fileRelevantPath comming after uploading a file to the temp folder path 
         * It contains the temp folder names
         */
        $this->initExportedDataFilesInfoManager();

        $fileName = $this->getFileDefaultName($fileRelevantPath); 

        $fileRealPath = CustomFileHandler::getFileStoragePath($fileRelevantPath , $this->tempFilesDisk);

        return $this->exportedDataFilesInfoManager->addNewFileInfo( $fileName , $fileRealPath , $fileRelevantPath )
                                                  ->SaveChanges();
    }
    
    protected function getOldDataExportersDeleterJobClass() : string
    {
        return Exporter::getOldDataExportersDeleterJobClass() ?? OldDataExportersDeleterJob::class;
    }

    public static function sceduleOldDataExportersDeleterJob(Schedule $schedule) : void
    {
        $jobClass = static::getOldDataExportersDeleterJobClass();

        $schedule->job( $jobClass )->daily()->at('00:00');
    }

}
