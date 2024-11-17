<?php

namespace ExpImpManagement\ExportersManagement\ExportedFilesProcessors;


use ExpImpManagement\ExportersManagement\ExportedFilesProcessors\Traits\ExportedDataFilesInfoManagerMethods;
use TemporaryFilesHandlers\TemporaryFilesProcessors\TemporaryFilesProcessor;
use Exception;

class ExportedFilesProcessor extends TemporaryFilesProcessor
{

    use  ExportedDataFilesInfoManagerMethods;

       /**
     * @param string $filePathToUpload
     * @param string $fileName
     * @param string $fileFolderRelevantPath
     * @throws Exception
     * @return string
     * Returns Uploaded File's Relevant path in Storage (need to concatenate it with storage main path  )
     */
    public function uploadToStorage(string $filePathToUpload , string $fileName = "", string $fileFolderRelevantPath = "" ) : string
    {
        $fileNewRelevantPath =  parent::uploadToStorage(  $filePathToUpload ,   $fileName  ,   $fileFolderRelevantPath   );
        $this->informExportedDataFilesInfoManager($fileNewRelevantPath);
        return $fileNewRelevantPath;
    } 
}
