<?php


namespace ExpImpManagement\ExportersManagement\ExporterTypes\CSVExporter\Traits;
 
trait CSVExporterSerilizing
{

    protected function getSerlizingProps() : array
    {
        $parentProps = parent::getSerlizingProps();
        $parentProps[] =  "importableFormatFactory";
        return $parentProps;
    }

    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data)  ||
               ! array_key_exists('importableFormatFactory' , $data) ;
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data); 
        $this->useImportableFormatFileFactory($data["importableFormatFactory"]);
    } 
}