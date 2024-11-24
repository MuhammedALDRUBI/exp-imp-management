<?php

namespace ExpImpManagement\ExportersManagement\ExportingBaseService;

use Exception;
use ExpImpManagement\ExportersManagement\Exporter\Exporter; 
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class ExportingBaseService
{
    use ExportingBaseServiceValidationMethods;

    protected ?string $ModelClass = null; 
    protected Exporter $exporter;
   
    abstract protected function initExporter($exporterType) : Exporter;
    public function __construct(?string $ModelClass = null)
    {
        $this->validateRequest();
        $this->setModelClass($ModelClass);
        $this->setExporter();
    }
 
    protected function setModelClass(?string $ModelClass = null)
    {
        $this->ModelClass = $ModelClass; // no need to validate class ... the exporter will validate its requirements
    }
    
    protected function setExporter() : void
    {
        $exporterType = $this->data["type"]; 
        $this->exporter = $this->initExporter(  $exporterType ); 
    }

    /**
     * Any access to the exporter will be by basicExport or callOnExporter
     */
    protected function getExporter() : Exporter
    { 
        return $this->exporter;
    }

    public function basicExport(string $documentTitle) : JsonResponse | StreamedResponse
    { 
        return $this->getExporter()->export($documentTitle);
    }

    public function callOnExporter(callable $callback)
    {
        return call_user_func($callback , $this->getExporter());
    }

}
