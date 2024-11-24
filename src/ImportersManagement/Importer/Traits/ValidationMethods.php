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

    protected function getUploadedFileValidationRequestFormClass() : string
    {
        return UploadedFileRequestForm::class;
    }
 
    protected function getUploadedFileRequestKey()  : string
    {
        return "file";
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

    protected function validateUploadedFile() : self
    {
        $this->validationManager->setBaseRequestFormClass( $this->getUploadedFileValidationRequestFormClass() )
                                ->startGeneralValidation();
        return $this;
    }
 
    protected function validateSingleModel(array $modelData) : void
    {
        $this->validationManager->setBaseRequestFormClass($this->getDataValidationRequestForm())
                                ->validateSingleModelRowKeys($modelData);
    }
}