<?php

namespace ExpImpManagement\ExportersManagement\ExportedFilesProcessors;


use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\Traits\ExportedDataFilesInfoManagerMethods;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;
use Exception;
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

       /**
     * @param string $filePathToUpload
     * @param string $fileName
     * @param string $fileFolderRelevantPath
     * @throws Exception
     * @return string
     * Returns Uploaded File's Relevant path in Storage (need to concatenate it with storage main path  )
     */
    // public function uploadToStorage(string $filePathToUpload , string $fileName = "" ) : string
    // {
    //     $fileNewRelevantPath =  parent::uploadToStorage(  $filePathToUpload ,   $fileName      );
    //     $this->informExportedDataFilesInfoManager($fileNewRelevantPath);
    //     return $fileNewRelevantPath;
    // } 
}
