<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\CSVFileContentProcessor;
use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use Maatwebsite\Excel\Concerns\WithHeadings;

abstract class CSVImporter extends Importer
{

    abstract public function getImportableFileFormatFactory() : CSVImportableFileFormatFactory;

    public function downloadFormat()
    {
        return $this->getImportableFileFormatFactory()->downloadFormat();
    }
    
    /**
     * Will Be Overridden In Child Classes (Based On Type)
     * @return string
     */
    protected function getDataFileExpectedExtension(): string
    {
        return "csv";
    }

    protected function getDataFileContentProcessor() : DataFileContentProcessor
    {
        return new CSVFileContentProcessor();
    }

    protected function setModelDesiredColumns() : self
    {
        $formatFactory = $this->getImportableFileFormatFactory();
        if($formatFactory instanceof WithHeadings)
        {
            $this->ModelDesiredColumns = $formatFactory->headings();
            return $this;
        }
        return  parent::setModelDesiredColumns();
    }
}
