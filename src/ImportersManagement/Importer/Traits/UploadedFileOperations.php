<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;
 
use ExpImpManagement\ImportersManagement\Importer\Importer;  
use Exception;
use Illuminate\Http\UploadedFile;

trait UploadedFileOperations
{   
    protected ?UploadedFile     $uploadedFile = null; 
    protected ?string $uploadedFileTempRealPath = null;
    protected string            $UploadedFileFullName = ""; 
  
  
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
    public function setUploadedFileTempRealPath(?string $uploadedFileTempRealPath = null ): Importer
    {
        if(!$uploadedFileTempRealPath)
        {
           $uploadedFileTempRealPath = $this->getUploadedFileTempRealPath(); 
        }
        $this->uploadedFileTempRealPath = $uploadedFileTempRealPath;
        return $this;
    }

    protected function setUploadedFileFullName(?string $uploadedFileName = null) : Importer
    {
        if(!$uploadedFileName)
        {
            $uploadedFileName = $this->uploadedFile->getClientOriginalName();
        }
        $this->UploadedFileFullName = $uploadedFileName;

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
 
   protected function isUploadedFileExistInTempPath() : bool
   {
        return $this->filesProcessor->IsFileExists($this->uploadedFileTempRealPath);
   }
 
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
        return $this;
    }
 
    protected function deleteTempUploadedFile() : self
    {
        $this->filesProcessor->deleteFile($this->uploadedFileTempRealPath); 
        return $this;
    } 
}