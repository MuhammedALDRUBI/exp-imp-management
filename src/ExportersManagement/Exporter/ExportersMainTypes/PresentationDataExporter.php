<?php

namespace ExpImpManagement\ExportersManagement\Exporter\ExportersMainTypes;

use ExpImpManagement\ExportersManagement\Exporter\Exporter;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor;
use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\PresentationDataArrayProcessors\PresentationDataArrayProcessor;

abstract class PresentationDataExporter extends Exporter
{
    protected function getFinalDataArrayProcessor(): DataArrayProcessor
    {
        return new PresentationDataArrayProcessor();
    }
}
