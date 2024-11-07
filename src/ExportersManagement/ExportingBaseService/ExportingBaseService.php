<?php

namespace ExpImpManagement\ExportersManagement\ExportingBaseService;

use Exception;
use ExpImpManagement\ExportersManagement\Exporter\Exporter; 
use Illuminate\Http\JsonResponse; 

abstract class ExportingBaseService
{
    use ExportingBaseServiceValidationMethods;

    abstract protected function getExportTypesMap()  :array;
   
    protected function initExporter($exporterTypeClass) : Exporter
    {
        if(!is_subclass_of($exporterTypeClass , Exporter::class))
        {
            throw new Exception("Exporter class is not exists or not a valid Exporter type");
        }
        return app()->make($exporterTypeClass);
    }

    protected function getExporter() : Exporter
    {
        $exportersMap = $this->getExportTypesMap();
        $exporterType = $this->data["type"];

        if(!array_key_exists($exporterType , $exportersMap))
        {
            throw new Exception("File Type Is not supported now");
        }
        return $this->initExporter( $exportersMap[$exporterType] ); 
    }

    public function export() : JsonResponse
    { 
        return $this->validateRequest()->getExporter()->export();
    }

}
