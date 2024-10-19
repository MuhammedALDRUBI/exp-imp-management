<?php

namespace ExpImpManagement\ExportersManagement\ExporterBuilder;

use ExpImpManagement\ExportersManagement\Exporter\Exporter; 
use Exception;
use Illuminate\Http\Request;

abstract class ExporterBuilder
{
    use BuilderValidationMethods;

    abstract protected function getExportTypesMap()  :array;

    /**
     * @param Request|array $request 
     * @throws Exception
     */
    public function __construct(Request | array $request)
    {
        $this->validateRequest($request)->validateTypeValue();
    }

    public function build() : Exporter
    {
        $class = $this->getExportTypesMap()[$this->data["type"]];
        return new $class;
    }

}
