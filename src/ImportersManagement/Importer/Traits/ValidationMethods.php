<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use CRUDServices\ValidationManagers\ManagerTypes\StoringValidationManager;
use ExpImpManagement\ImportersManagement\RequestForms\UploadedFileRequestForm; 
use Illuminate\Http\UploadedFile; 

trait ValidationMethods
{

    protected ?StoringValidationManager $validationManager = null;
    protected string $dataValidationRequestFormClass;

    protected function getDataValidationRequestFormClass() : string
    {
        return $this->dataValidationRequestFormClass;
    }

    public function setDataValidationRequestFormClass(string $requestFormClass)  : self
    {
       //there is no need to check the type of class ... the ValidatorLib package will throw an exception if the the type 
       //is not compatiable with it
       $this->dataValidationRequestFormClass = $requestFormClass;
        return $this;
    }

    protected function initValidationManager() : StoringValidationManager
    {
        return StoringValidationManager::Singleton();
    }
    protected function setValidationManager() : self
    {
        $this->validationManager = $this->initValidationManager();
        return $this;
    }

    protected function getValidUploadedFile() : UploadedFile
    {
        return $this->validationManager->getRequestValidData()[ $this->getUploadedFileRequestKey() ];
    }

    protected function getTableTruncatingRequestingStatus() : bool
    {
        return $this->validationManager->getRequestValidData()[ $this->getTableTruncatingRequestingStatusKey() ] ?? false;
    }
    
    /**
     * UploadedFile validation methods
     */
    
     protected function getUploadedFileValidationRequestFormClass() : string
     {
         return UploadedFileRequestForm::class;
     }
  
     protected function getUploadedFileRequestKey()  : string
     {
         return "file";
     }

     protected function getTableTruncatingRequestingStatusKey()  : string
     {
        return "truncateTable";
     }

    protected function validateUploadedFile() : self
    {
        $this->validationManager->setBaseRequestFormClass( $this->getUploadedFileValidationRequestFormClass() )
                                ->startGeneralValidation();
        return $this;
    }
 
    /**
     * Data Validation part
     */


    protected function prepareValidationManagerForDataValidation() : void
    {
        $this->validationManager->setBaseRequestFormClass($this->getDataValidationRequestFormClass());
    }

    protected function validateFileSingleDataRow(array $fileSingleRow) : void
    {
        $this->validationManager->setValidatorData($fileSingleRow)->startGeneralValidation();
    }

    protected function validateSingleModelData(array $modelData) : void
    {
        $this->validationManager->validateSingleModelRowKeys($modelData);
    }

    protected function validateRelationshipSingleDataRow(string $relationshipName , array $singleDataRow = []  ) : void
    {
        $this->validationManager->validateRelationshipSingleRowKeys( $relationshipName ,  $singleDataRow   );
    }

}