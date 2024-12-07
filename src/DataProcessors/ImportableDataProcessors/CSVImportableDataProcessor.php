<?php

namespace ExpImpManagement\DataProcessors\ImportableDataProcessors;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class CSVImportableDataProcessor extends ImportableDataProcessor
{

    protected CSVImportableFileFormatFactory $factory;

    public function __construct(CSVImportableFileFormatFactory $factory)
    {
        $this->setCSVImportableFileFormatFactory($factory);
    }

    public function setCSVImportableFileFormatFactory(CSVImportableFileFormatFactory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    public function getCSVImportableFileFormatFactory()
    {
        return $this->factory;
    } 
    protected function appendRelationshipProps(array $dataRow , array &$processedDataRow) : void
    {
        foreach($this->getCSVImportableFileFormatFactory()->getRelationshipColumnComponents() as $relationshipName => $columnComponents)
        {
            $relationshipData = [];
            foreach($columnComponents as $columnComponent)
            {
                $relationshipData[ $columnComponent->getDatabaseFieldName() ] = $dataRow[ $columnComponent->getColumnHeaderName() ] ?? null;
            }

            $processedDataRow[$relationshipName] = $relationshipData;
        }
    }

    protected function getModelProps(array $dataRow ) : array
    {
        $modelProps = [];
        foreach($this->getCSVImportableFileFormatFactory()->getModelColumnComponents() as $columnComponent)
        {
            $modelProps[ $columnComponent->getDatabaseFieldName() ] = $dataRow[ $columnComponent->getColumnHeaderName() ] ?? null;
        }
        return $modelProps;
        // $modelKeys = $this->getCSVImportableFileFormatFactory()->getModelHeadings();
        // return array_intersect_key($dataRow, array_flip($modelKeys));
    }

    protected function getProcessedDataRow(array $dataRow) : array
    {
        $processedDataRow = $this->getModelProps($dataRow); 
        $this->appendRelationshipProps($processedDataRow , $dataRow);
        return $processedDataRow;
    } 
  
}