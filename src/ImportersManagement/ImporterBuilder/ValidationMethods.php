<?php

namespace ExpImpManagement\ImportersManagement\ImporterBuilder;

use ExpImpManagement\ImportersManagement\RequestForms\DataImporterRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ValidatorLib\ArrayValidator;
use ValidatorLib\Validator;

trait ValidationMethods
{

    protected ?Validator $validator = null;
    protected UploadedFile $file ;

    protected function getRequestFormClass() : string
    {
        return DataImporterRequest::class;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getNeededImporterExtension() : string
    {
        $fileExtension = $this->file->getClientOriginalExtension();
        if(!array_key_exists($fileExtension , $this->getImporterTypesMap() ) )
        {
            throw new  Exception("It is not supported to Import These File types For This Module");
        }
        return $fileExtension;
    }

    /**
     * @param Request|array $request
     * @return ImporterBuilder|ValidationMethods
     * @throws Exception
     */
    protected function initValidator(Request | array $request)  :self
    {
        if($this->validator){return $this;}
        $this->validator = new ArrayValidator( $this->getRequestFormClass() ,$request);
        return $this;
    }

    /**
     * @param Request|array $request
     * @return ImporterBuilder|ValidationMethods
     * @throws Exception
     */
    protected function validateOperation(Request | array $request) :self
    {
        $this->initValidator($request);
        $validationResult = $this->validator->validate();
        if(is_array($validationResult) ){ throw new Exception( join(" , " , $validationResult) ); }
        $this->file = $this->validator->getRequestData()["file"];
        return $this;
    }

}
