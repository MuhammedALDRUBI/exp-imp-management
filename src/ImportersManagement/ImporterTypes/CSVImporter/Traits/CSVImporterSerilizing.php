<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits;
 
trait CSVImporterSerilizing
{

    protected function getSerlizingProps() : array
    {
        $parentProps = parent::getSerlizingProps();
        $parentProps[] = 'importableTemplateFactory';
        return $parentProps;
    }
  
    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) ||  ! array_key_exists('importableTemplateFactory' , $data) ;
    }
    
    protected static function checkRequiredProps($data) : void
    {
        if( static::DoesItHaveMissedSerlizedProps($data) )
        {
            static::throwUnerilizableObjectException();
        }
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data);

        $this->setImportableFileFormatFactory($data["importableTemplateFactory"]);
    } 
  
}