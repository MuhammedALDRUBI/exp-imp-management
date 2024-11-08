<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use CRUDServices\ValidationManagers\ManagerTypes\StoringValidationManager;
use ExpImpManagement\ImportersManagement\RequestForms\UploadedFileRequestForm; 
use Illuminate\Http\UploadedFile;
use Throwable;  

trait ValidationMethods
{

    protected ?StoringValidationManager $validationManager = null;
    protected string $DataValidationRequestForm;


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

    // protected function validateDataGenerally() : void
    // {
    //     $this->validationManager->setBaseRequestFormClass($this->getDataValidationRequestForm())
    //                             ->setValidatorData($this->ImportedDataArray)
    //                             ->startGeneralValidation();
    // }

    protected function validateSingleModel(array $modelData) : void
    {
        try
        {
            $this->validationManager->validateSingleModelRowKeys($modelData);

        }catch(Throwable $e)
        {
            $this->singleRowValidationFailed($modelData , $e);
        }
    }
}