<?php

namespace ExpImpManagement\ExportersManagement\ExporterBuilder;

use ExpImpManagement\ExportersManagement\RequestForms\DataExporterRequest;
use Exception;
use Illuminate\Http\Request; 
use ValidatorLib\ArrayValidator;
use ValidatorLib\Validator;

trait BuilderValidationMethods
{

    protected Validator $validator;
    protected array $data;

    protected function getRequestFormClass() : string
    {
        return DataExporterRequest::class;
    }

    /**
     * @param Request|array $request
     * @return ExporterBuilder|BuilderValidationMethods
     * @throws Exception
     */
    protected function initValidator(Request | array $request) : self
    {
        $this->validator = new ArrayValidator($this->getRequestFormClass() , $request);
        return $this;
    }

    /**
     * @param Request|array $request
     * @return ExporterBuilder|BuilderValidationMethods
     * @throws Exception
     */
    protected function validateRequest(Request | array $request) : self
    {
        $this->initValidator($request);
        $validationResult = $this->validator->validate();
        if(is_array($validationResult)){throw new Exception( join( " , " , $validationResult) ) ;}
        $this->data = $this->validator->getRequestData();
        return $this;
    }

    /**
     * @return ExporterBuilder|BuilderValidationMethods
     * @throws Exception
     */
    protected function validateTypeValue() : self
    {
        if(!array_key_exists($this->data["type"] , $this->getExportTypesMap() ) )
        {
            throw new Exception("File Type Is not supported now");
        }
        return $this;
    }

}
