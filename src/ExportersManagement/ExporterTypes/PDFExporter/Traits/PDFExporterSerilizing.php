<?php


namespace ExpImpManagement\ExportersManagement\ExporterTypes\PDFExporter\Traits;
 
trait PDFExporterSerilizing
{
 
    protected function setUnserlizedProps($data)
    {
        parent::setUnserlizedProps($data);
        $this->setViewTemplateRelativePath($data["viewTemplateRelativePath"]);
    }
 
    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) || !array_key_exists("viewTemplateRelativePath" , $data);
    }

    protected function getSerlizingProps() : array
    {
        $parentProps = parent::getSerlizingProps();
        $parentProps[] = "viewTemplateRelativePath";
        return $parentProps;
    }

}