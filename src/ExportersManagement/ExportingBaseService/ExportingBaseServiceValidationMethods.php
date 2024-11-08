<?php

namespace ExpImpManagement\ExportersManagement\ExportingBaseService;

use ExpImpManagement\ExportersManagement\RequestForms\DataExporterRequest;
use Exception;
use ValidatorLib\JSONValidator;
use ValidatorLib\Validator;

trait ExportingBaseServiceValidationMethods
{

    protected Validator $validator;
    protected array $data;

    protected function getRequestFormClass() : string
    {
        return DataExporterRequest::class;
    }

    protected function setRequestData() : void
    {
        $this->data = $this->validator->getRequestData();
    }
    /**
     * @throws Exception
     */
    protected function initValidator() : Validator
    {
        $this->validator = new JSONValidator($this->getRequestFormClass());
        return $this->validator;
    }

    /**
     * @throws Exception
     */
    protected function validateRequest() : self
    {
        $this->initValidator()->validate(); 
       $this->setRequestData();
       return $this;
    }
 

}
