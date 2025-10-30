<?php

namespace ExpImpManagement\ExportersManagement\Interfaces;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;

interface ExportsCSVImportableData
{

    public function getCSVImportableFileFormatFactory() : CSVImportableFileFormatFactory;
    
}