<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter;

use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\CSVFileContentProcessor;
use ExpImpManagement\ImportersManagement\DataFilesContentProcessors\DataFileContentProcessor;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\CSVImporterSerilizing;
use ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\DataImportingFailingHandling;
use ExpImpManagement\ImportersManagement\ImportingFilesProcessors\CSVImportingFilesProcessor;
use Illuminate\Notifications\Notification;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * @prop CSVImportingFilesProcessor $filesProcessor
 */
class CSVImporter extends Importer
{
    use DataImportingFailingHandling , CSVImporterSerilizing;

    protected CSVImportableFileFormatFactory $importableTemplateFactory;

    
    public function __construct(string $ModelClass , string $dataValidationRequestFormClass , CSVImportableFileFormatFactory $templateFactory)
    {
        parent::__construct($ModelClass , $dataValidationRequestFormClass);
        $this->setImportableFileFormatFactory( $templateFactory );
    }

    public function getImportableFileFormatFactory() : CSVImportableFileFormatFactory
    {
        return $this->importableTemplateFactory;
    }

    public function setImportableFileFormatFactory(CSVImportableFileFormatFactory $templateFactory) : self
    {
        $this->importableTemplateFactory = $templateFactory;
        return $this;
    }


    public function downloadFormat()
    {
        return $this->getImportableFileFormatFactory()->downloadFormat();
    }

    protected function initFileProcessor() : Importer
    {
        if(!$this->filesProcessor)
        {
             $this->filesProcessor = new CSVImportingFilesProcessor(); 
        }
        return $this;
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
  

    protected function importData() : Importer
    {
        parent::importData();

        /**
         * This is another format contains the rows have not stored in database .... this format also has DataValidation on each Cell 
         * it is used to allow user to know which rows aren't stored and chaging them manually
         */
        if($this->DoesItHaveRejectedRow() &&  $fileContent = $this->getRejectedFileContent() )
        {
            $this->setRejectedDataFileName()->setRejectedDataFilePath();

            //after this method the file will be copied to the temp path in the storage
            $this->processRejectedDataFile($fileContent);
  
            // nothing to do here ... the file already copied and the path will be passed to the convenient notification 
        }
        
        return $this;
    }

    public function getConvinientNotification() : Notification
    {
        if(!$this->rejectedDataFilePath)
        { 
            return parent::getConvinientNotification();
        }

        return $this->getRejectedDataFileNotification();
    }
}
