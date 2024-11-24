<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use Exception;
use ExpImpManagement\ExportersManagement\Interfaces\SelfConstructableExporter;

trait ExporterSelfConstructing
{
 
    public function __construct()
    {
        if(!$this instanceof SelfConstructableExporter)
        {
            throw new Exception("ExporterSelfConstructing trait must be used in a SelfConstructableExporter typed class only ");    
        }

        parent::__construct( $this->getModelClassForSelfConstructing()  );
        
    }

}