<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter;

use ExpImpManagement\DataProcessors\ImportableDataProcessors\CSVImportableDataProcessor;
use ExpImpManagement\DataProcessors\ImportableDataProcessors\ImportableDataProcessor;
use ExpImpManagement\ImportersManagement\DataFilesContentExtractors\CSVDataFilesContentExtractor;
use ExpImpManagement\ImportersManagement\DataFilesContentExtractors\DataFilesContentExtractor; 
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;
use ExpImpManagement\ImportersManagement\Importer\Importer;
use ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\CSVImporterSerilizing;
use ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\DataImportingFailingHandling;
use ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\RelationshipImportingMethods;
use ExpImpManagement\ImportersManagement\ImportingFilesProcessors\CSVImportingFilesProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notification;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Throwable;

/**
 * @prop CSVImportingFilesProcessor $filesProcessor
 */
class CSVImporter extends Importer
{
    use DataImportingFailingHandling , CSVImporterSerilizing , RelationshipImportingMethods;

    protected CSVImportableFileFormatFactory $importableTemplateFactory;

    protected array $ModelFillableColumns = [];
    protected array $relationshipsFillables = [];
    
    protected array $modelColumnsNeedUserDisplayValueReplacement = [];
    protected array $relationColumnsNeedUserDisplayValueReplacement = [];


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

    protected function getDefaultImportableDataProcessor() : ImportableDataProcessor
    {
        return new CSVImportableDataProcessor( $this->getImportableFileFormatFactory() );
    }

    protected function getDataFilesContentExtractor() : DataFilesContentExtractor
    {
        return new CSVDataFilesContentExtractor();
    }

    protected function doesnModelColumnNeedUserDisplayValueReplacement(?string $userDisplayValue = null) : bool
    {
        return in_array($userDisplayValue , $this->modelColumnsNeedUserDisplayValueReplacement);
    }

    protected function getModelDbStoringValue(?string $userDisplayValue = null) : string|array|null
    {
        if($this->doesnModelColumnNeedUserDisplayValueReplacement($userDisplayValue))
        {
            return $this->getImportableFileFormatFactory()->getModelDbStoringValue($userDisplayValue);
        }

        return $userDisplayValue;
    }

    protected function getCurrentModelFillableValues(array $row) : array
    {
        $columnsValues = [] ;

        foreach ($this->getModelFillableColumns() as $column)
        {
            if(isset($row[$column]))
            {
                $initValue =  $row[$column] ?: null ;
                $columnsValues[$column] = $this->getModelDbStoringValue($initValue);
            }
        }
        return $columnsValues;
    }


    protected function setModelFillableColumns() : self
    {
        $this->ModelFillableColumns = $this->getImportableFileFormatFactory()->getModelDatabaseFields();
        return $this; 
    }

    protected function setModelColumnsNeedUserDisplayValueReplacement() : self
    {
        $this->modelColumnsNeedUserDisplayValueReplacement = $this->getImportableFileFormatFactory()->getModelDisplayValueReplacmentNeedingColumnFieldNames();
        return $this;
    }
 
    protected function getModelFillableColumns() : array
    {
        return $this->ModelFillableColumns;
    }

    protected function importDataRows() : void
    {
        $this->setModelFillableColumns();
        $this->setModelColumnsNeedUserDisplayValueReplacement();
        parent::importDataRows();
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
