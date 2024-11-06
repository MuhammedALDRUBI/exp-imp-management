<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\DataFilesInfoManagers\ExportedDataFilesInfoManager\ExportedDataFilesInfoManager;
use ExpImpManagement\DataFilesInfoManagers\ImportableDataFilesInfoManager\ImportableDataFilesInfoManager;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use TemporaryFilesHandlers\TemporaryFilesCompressors\TemporaryFilesCompressor;
use CustomFileSystem\CustomFileDeleter;
use CustomFileSystem\S3CustomFileSystem\CustomFileDeleter\S3CustomFileDeleter;
use Exception;
use Illuminate\Http\UploadedFile;

trait UploadedFileOperations
{ 
    protected ?CustomFileDeleter $customFileDeleter = null;
    protected ?ImportableDataFilesInfoManager   $importableDataFilesInfoManager = null; 


    protected ?UploadedFile $uploadedFile = null;
    protected string        $uploadedFileStorageRelevantPath = "";
    protected string        $UploadedFileFullName = ""; 

    //this prop will be kept to be passed by the job into the importer at background
    protected bool $ImportedDataFileAfterProcessingDeletingStatus = false;
 
    /**
     * @return ImportableDataFilesInfoManager
     */
    protected function initImportableDataFilesInfoManager() : ImportableDataFilesInfoManager
    {
        if(!$this->importableDataFilesInfoManager){$this->importableDataFilesInfoManager = new ImportableDataFilesInfoManager();}
        return $this->importableDataFilesInfoManager;
    }
 
    /**
     * @param string $UploadedFileFullName
     * @return string
     * @throws Exception
     */
    protected function uploadToImportedFilesTempStorage(string $UploadedFileFullName)  : string
    {
        $this->DeleteUploadedFileAfterProcessing = true;
        return $this->filesProcessor->uploadToStorage(
                                        $this->uploadedFile->getRealPath() ,
                                        $UploadedFileFullName ,
                                        Importer::ImportedUploadedFilesTempFolderName
                                    );
    } 

    /**
     * @return string
     * @throws JsonException
     * @throws JsonException
     * @throws Exception
     * The Uploaded File Will Be Uploaded To Storage Temp Path To Process It Later , Then Deleting It After Processing
     */
    protected function getUploadedFileStorageRelevantPath() : string
    {   
        return $this->uploadToImportedFilesTempStorage($this->UploadedFileFullName);
    }

    /**
     * @param string $uploadedFileStorageRelevantPath
     * @return Importer
     * @throws Exception
     */
    public function setUploadedFileStorageRelevantPath(string $uploadedFileStorageRelevantPath = "" ): Importer
    {
        if(!$uploadedFileStorageRelevantPath)
        {
             $uploadedFileStorageRelevantPath = $this->getUploadedFileStorageRelevantPath(); 
        }
        $this->uploadedFileStorageRelevantPath = $uploadedFileStorageRelevantPath;
        return $this;
    }

   /**
     * @param bool $status
     * @return $this
     * @throws Exception
     */
    public function setImportedDataFileAfterProcessingDeletingStatus(bool $status): self
    {
        $this->ImportedDataFileAfterProcessingDeletingStatus = $status;
        return $this;
    }


    protected function setUploadedFileFullName() : Importer
    {
        $this->UploadedFileFullName = $this->uploadedFile->getClientOriginalName();
        return $this;
    }

    
    /**
     * @return Importer
     */
    protected function setUploadedFile(): Importer
    {
        $this->uploadedFile = $this->getValidUploadedFile();
        return $this;
    }


    /**
     * @return Importer
     * @throws Exception
     */
    protected function HandleUploadedFile() : Importer
    {
        return $this->validateUploadedFile()->setUploadedFile()->setUploadedFileFullName()->setUploadedFileStorageRelevantPath() ; 
    }

   
    /**
     * @return Importer
     * @throws Exception
     */
    protected function checkUploadedFileStorageRelevantPath() : void
    {
        if(!$this->uploadedFileStorageRelevantPath)
        {
            throw new Exception("There Is No Uploaded File Storage Relevant Path's Value , Can't Access To Imported Data File To Complete Operation !");
        }
    }
 
    /**
     * @return Importer
     * @throws JsonException
     * @throws Exception
     */
    protected function openImportedDataFileForProcessing() : Importer
    {
        $this->checkUploadedFileStorageRelevantPath();

        /**  Copying Data File From Storage To Tem Files Folder .... And Set New Copy To ImportedFileTempRealPath temp folder */
        $tempFilesPath = $this->filesProcessor->addTempFileToCopy( $this->uploadedFileStorageRelevantPath)->copyToTempPath();
        $this->uploadedFileTempRealPath =  rtrim($tempFilesPath ,"/") . "/"  .  $this->UploadedFileFullName ;
        return $this;
    }


    protected function initCustomFileDeleter(): CustomFileDeleter
    {
        if(!$this->customFileDeleter) { $this->customFileDeleter = new S3CustomFileDeleter(); }
        return $this->customFileDeleter;
    }

    protected function deleteTempUploadedFile() : self
    {
        /**
         * Need to determine what will happen for the uploadedFile in the storage path 
         */
        $this->initCustomFileDeleter()->deleteFileIfExists($this->uploadedFileStorageRelevantPath);
        return $this;
    }
}
