<?php

namespace ExpImpManagement\DataProcessors\ExportableDataProcessors;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory;


class CSVExportableDataProcessor extends ExportableDataProcessor
{

    protected CSVImportableFileFormatFactory $factory ;

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
        $relationshipColumnComponents = $this->getCSVImportableFileFormatFactory()->getRelationshipColumnComponents() ;

        foreach($relationshipColumnComponents as $relationshipName => $columnComponents)
        { 
            if(
                !isset( $dataRow[$relationshipName]) 
                ||
                !is_array($relationshipData = $dataRow[$relationshipName])
                || empty($relationshipData)
              )
            {
                continue;
            }

            foreach($columnComponents as $columnComponent)
            {
                $processedDataRow[ $columnComponent->getColumnHeaderName() ] 
                =
                $relationshipData[ $columnComponent->getDatabaseFieldName() ] ?? null;
            } 
        }
    }

    protected function getModelProps(array $dataRow ) : array
    {
        $modelProps = [];
        foreach($this->getCSVImportableFileFormatFactory()->getModelColumnComponents() as $columnComponent)
        {
            $modelProps[ $columnComponent->getColumnHeaderName() ] 
            =
            $dataRow[ $columnComponent->getDatabaseFieldName() ] ?? null;
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