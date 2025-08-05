<?php

namespace ExpImpManagement\DataProcessors\ImportableDataProcessors;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;

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
        $relationshipColumnComponents = $this->getCSVImportableFileFormatFactory()->getRelationshipColumnComponents();
        
        foreach($relationshipColumnComponents as $relationshipName => $columnComponents)
        {
            $relationshipData = [];
            foreach($columnComponents as $columnComponent)
            {
                $relationshipData[ $columnComponent->getDatabaseFieldName() ] 
                =
                $dataRow[ $columnComponent->getColumnHeaderName() ] ?? null;
            }

            $processedDataRow[$relationshipName] = $relationshipData;
        }
    }

    protected function getModelProps(array $dataRow ) : array
    {
        $modelProps = [];
        foreach($this->getCSVImportableFileFormatFactory()->getModelColumnComponents() as $columnComponent)
        {
            $modelProps[ $columnComponent->getDatabaseFieldName() ] 
            =
            $dataRow[ $columnComponent->getColumnHeaderName() ] ?? null;
        }
        
        return $modelProps; 
    }

    protected function getProcessedDataRow(array $dataRow) : array
    {
        $processedDataRow = $this->getModelProps($dataRow); 
        $this->appendRelationshipProps( $dataRow , $processedDataRow); 
        return $processedDataRow;
    } 
  
}