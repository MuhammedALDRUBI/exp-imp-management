<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\Interfaces\MustImportFiles;
use CustomFileSystem\CustomFileUploader;
use CustomFileSystem\S3CustomFileSystem\CustomFileUploader\S3CustomFileUploader;


trait FilesImportingMethods
{
    protected ?CustomFileUploader $customFileUploader = null;

    protected function getAllowedExtensions() : array
    {
        return ["pdf" , "png" , "jpg" , "jpeg" , "csv" , "xlsx" , "gif" ];
    }


    protected function validateFilesExtensions(array $filesArrayToValidate) : array
    {
        $validFilePaths = [];
        foreach ($filesArrayToValidate as $filePath)
        {
            if($filePath == $this->getDataFilePath()){continue;}
            if(in_array(
                $this->filesProcessor::getFileExtension($filePath) ,
                $this->getAllowedExtensions()
            )){
                $validFilePaths[] = $filePath;
            }
        }
        return $validFilePaths;
    }

    protected function getImportableFilePaths() : array
    {
        $files = $this->filesProcessor::getFolderFiles($this->ExtractedUploadedFileTempRealPath);
        return $this->validateFilesExtensions($files);
    }

    protected function initFileUploader() : CustomFileUploader
    {
        if(!$this->customFileUploader){ $this->customFileUploader = new S3CustomFileUploader(); }
        return $this->customFileUploader;
    }

    protected function deleteImportedFiles() : Importer
    {
        return $this;
    }

    protected function importFiles() : Importer
    {
        if(!$this instanceof MustImportFiles){return $this;}
        $files = $this->getImportableFilePaths();
        $this->initFileUploader()->getUploadedFile();
    }

}
