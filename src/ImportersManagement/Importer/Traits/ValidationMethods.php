<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use CRUDServices\ValidationManagers\ManagerTypes\StoringValidationManager;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use Exception;
use ExpImpManagement\ImportersManagement\RequestForms\UploadedFileRequestForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ValidatorLib\ArrayValidator;
use ValidatorLib\Validator;

trait ValidationMethods
{

    protected ?StoringValidationManager $validationManager = null;
    protected string $DataValidationRequestForm;

    abstract protected function getDataValidationRequestForm() : string;

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
    protected function setValidationManger() : self
    {
        $this->validationManager = $this->initValidationManager();
        return $this;
    }

    protected function getValidUploadedFile() : UploadedFile
    {
        return $this->validationManger->getRequestValidData()[ $this->getUploadedFileRequestKey() ];
    }

    protected function validateUploadedFile() : self
    {
        $this->validationManager->setBaseRequestFormClass( $this->getUploadedFileValidationRequestFormClass() )
                                ->startGeneralValidation();
        return $this;
    }

    protected function validateDataGenerally() : void
    {
        $this->validationManager->setBaseRequestFormClass($this->getDataValidationRequestForm())
                                ->setValidatorData($this->ImportedDataArray)
                                ->startGeneralValidation();
    }

    protected function validateSingleModel(array $modelData) : void
    {
        $this->validationManager->validateSingleModelRowKeys($modelData);
    }

    // /**
    //  * @return ValidationMethods|Importer
    //  * @throws JsonException
    //  */
    // public function setDataValidationRequestForm(): self
    // {
    //     $DataValidationRequestForm = $this->getDataValidationRequestForm();
    //     if(! class_exists($DataValidationRequestForm)){throw new Exception("The Given DataValidationRequestForm Is Not A valid Class Or Not Found !");}
    //     if(! (new $DataValidationRequestForm()) instanceof FormRequest){throw new Exception("The Given DataValidationRequestForm Is Not A Request Form Class !"); }
    //     $this->DataValidationRequestForm = $DataValidationRequestForm;
    //     return $this;
    // }

    // /**
    //  * @param array|Request $request
    //  * @return Importer
    //  * @throws Exception
    //  */
    // protected function initValidator(array | Request $request):Importer
    // {
    //     if($this->validator){return $this;}
    //     $this->validator = new ArrayValidator( $this->DataValidationRequestForm , $request );
    //     return $this;
    // }

    // /**
    //  * @param array $rules
    //  * @return Importer
    //  * @throws Exception
    //  */
    // protected function setValidationRulesOrDefaultRules(array $rules = []) : Importer
    // {
    //     if(!empty($rules))
    //     {
    //         $this->validator->OnlyRules( $rules );
    //         return $this;
    //     }
    //     $this->validator->AllRules();
    //     return $this;
    // }

    // /**
    //  * @return Importer
    //  * @throws Exception
    //  */
    // protected function setMultiRowInsertionRules() : Importer
    // {
    //     return $this->setValidationRulesOrDefaultRules(
    //                 $this->getMultiRowInsertionRules()
    //             );
    // }

    // /**
    //  * @return Importer
    //  * @throws Exception
    //  */
    // protected function setSingleRowInsertionRules() : Importer
    // {
    //     return $this->setValidationRulesOrDefaultRules(
    //                 $this->getSingleRowInsertionRules()
    //             );
    // }

    // /**
    //  * @return Importer
    //  * @throws Exception
    //  */
    // protected function validateFileData() : Importer
    // {
    //     return $this->initValidator($this->ImportedDataArray)->setMultiRowInsertionRules()->validOrFail();
    // }

    // /**
    //  * @param array $row
    //  * @return bool
    //  * @throws Exception | Exception
    //  */
    // protected function validateDataRow(array $row) : bool
    // {
    //     $this->validator->setRequestData($row);
    //     $this->setSingleRowInsertionRules();
    //     return $this->IsValid();
    // }

    // /**
    //  * @return ValidationMethods|Importer
    //  * @throws Exception
    //  */
    // protected function validOrFail() :self
    // {
    //     $validationResult = $this->validator->validate();
    //     if(is_array($validationResult)){ throw new Exception( join(" , " , $validationResult) );}
    //     return $this;
    // }

    // /**
    //  * @return bool
    //  */
    // protected function IsValid() :bool
    // {
    //     $validationResult = $this->validator->validate();

    //     /**  If $validationResult Is Not Array .... The Checked Data Is Valid */
    //     return !is_array($validationResult);
    // }

}
