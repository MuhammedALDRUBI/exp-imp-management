<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits;

use Exception;
use ExpImpManagement\ImportersManagement\Interfaces\SelfConstructableCSVImporter;

trait CSVImporterSelfConstructing
{
 
    public function __construct()
    {
        if(!$this instanceof SelfConstructableCSVImporter)
        {
            throw new Exception("CSVImporterSelfConstructing trait must be used in a SelfConstructableCSVImporter typed class only ");    
        }

        parent::__construct(
            $this->getModelClassForSelfConstructing() ,
            $this->getDataValidationRequestFormClassForSelfConstructing() ,
            $this->getImportableTemplateFactoryForSelfConstructing()
       );
        
    }

}