<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;
 
use ExpImpManagement\ImportersManagement\Importer\Importer; 
use CustomFileSystem\CustomFileDeleter;
// use CustomFileSystem\CustomFileHandler;
// use CustomFileSystem\S3CustomFileSystem\CustomFileDeleter\S3CustomFileDeleter;
use Exception;
use Illuminate\Http\UploadedFile;

trait UploadedFileOperations
{ 
    protected ?CustomFileDeleter $customFileDeleter = null; 


    protected ?UploadedFile     $uploadedFile = null;
    // protected ?string           $uploadedFileStorageRelevantPath = null;
    // protected ?string           $uploadedFileStorageRealPath = null;
    protected ?string $uploadedFileTempRealPath = null;
    protected string            $UploadedFileFullName = ""; 
  
 
    /**
     * @param string $UploadedFileFullName
     * @return string
     * @throws Exception
     */
    // protected function uploadToImportedFilesTempStorage(string $UploadedFileFullName)  : string
    // {  
    //     return $this->filesProcessor->uploadToStorage(
    //                                     $this->uploadedFile->getRealPath() ,
    //                                     $UploadedFileFullName 
    //                                 );
    // } 

    protected function uploadToImportedFilesTempFolder(string $UploadedFileFullName)  : string
    { 
        $tempFolderPath = $this->filesProcessor->HandleTempFileToCopy(
                                                                        $this->uploadedFile->getRealPath() ,
                                                                        $UploadedFileFullName
                                                                    )->copyToTempPath(); 
        return $tempFolderPath . $UploadedFileFullName;
    } 
    /**
     * @return string
     * @throws JsonException
     * @throws JsonException
     * @throws Exception
     * The Uploaded File Will Be Uploaded To Storage Temp Path To Process It Later , Then Deleting It After Processing
     */
    protected function getUploadedFileTempRealPath() : string
    {   
        return $this->uploadToImportedFilesTempFolder($this->UploadedFileFullName);
    }

    /**
     * @param string $uploadedFileStorageRelevantPath
     * @return Importer
     * @throws Exception
     */
    // public function setUploadedFileStorageRelevantPath(?string $uploadedFileStorageRelevantPath = null ): Importer
    // {
    //     if(!$uploadedFileStorageRelevantPath)
    //     {
    //        $uploadedFileStorageRelevantPath = $this->getUploadedFileStorageRelevantPath(); 
    //     }
    //     $this->uploadedFileStorageRelevantPath = $uploadedFileStorageRelevantPath;
    //     return $this;
    // }
  
        /**
     * @param string $uploadedFileStorageRelevantPath
     * @return Importer
     * @throws Exception
     */
    public function setUploadedFileTempRealPath(?string $uploadedFileTempRealPath = null ): Importer
    {
        if(!$uploadedFileTempRealPath)
        {
           $uploadedFileTempRealPath = $this->getUploadedFileTempRealPath(); 
        }
        $this->uploadedFileTempRealPath = $uploadedFileTempRealPath;
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
        return $this->validateUploadedFile()->setUploadedFile()->setUploadedFileFullName()->setUploadedFileTempRealPath() ; 
    }

//    protected function setUploadedFileStorageRealPath() : void
//    {
//         $this->uploadedFileStorageRealPath =  CustomFileHandler::getFileStoragePath($this->uploadedFileStorageRelevantPath) ;
//    }

//    protected function isUploadedFileExistInStorage() : bool
//    {
//         return CustomFileHandler::IsFileExists($this->uploadedFileStorageRelevantPath);
//    }
   protected function isUploadedFileExistInTempPath() : bool
   {
        return $this->filesProcessor->IsFileExists($this->uploadedFileTempRealPath);
   }
    /**
     * @return Importer
     * @throws Exception
     */
    // protected function checkUploadedFileStorageRelevantPath() : void
    // {
    //     if(!$this->uploadedFileStorageRelevantPath || !$this->isUploadedFileExistInStorage())
    //     {
    //         throw new Exception("There Is No Uploaded File Storage Relevant Path's Value , Can't Access To Imported Data File To Complete Operation !");
    //     }
    // }
    protected function checkUploadedFileStorageRelevantPath() : void
    {
        if(!$this->uploadedFileTempRealPath || !$this->isUploadedFileExistInTempPath())
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
        // $this->setUploadedFileStorageRealPath();
        return $this;
    }


    // protected function initCustomFileDeleter(): CustomFileDeleter
    // {
    //     if(!$this->customFileDeleter) 
    //     {
    //          $this->customFileDeleter = new S3CustomFileDeleter(); 
    //         }
    //     return $this->customFileDeleter;
    // }

    protected function deleteTempUploadedFile() : self
    {
        $this->filesProcessor->deleteFile($this->uploadedFileTempRealPath); 
        return $this;
    }
    // protected function deleteTempUploadedFile() : self
    // {
    //     /**
    //      * Need to determine what will happen for the uploadedFile in the storage path 
    //      */
    //    $this->initCustomFileDeleter()->deleteFileIfExists($this->uploadedFileStorageRelevantPath);
    //     return $this;
    // }
}
