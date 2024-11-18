<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents;

use Exception; 
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\CSVCellValidationDataTypeSetter;

class CSVFormatColumnInfoComponent extends FormatColumnInfoComponent
{
    
    // protected array $definedDataTypes = [ "date" , "decimal" , "list" , "textLength" , "time" ];
    protected string $columnCharSymbol;
    protected string $columnHeaderName;
    // protected string $dataType;
    protected ?int $width  = null;
    protected ?CSVCellValidationDataTypeSetter $cellValidationSetter = null;

    // protected ?array $validValues = null;

    public function __construct(string $columnCharSymbol , string $columnHeaderName  )
    {
        $this->setColumnCharSymbol($columnCharSymbol)->setColumnHeaderName($columnHeaderName) ;
    }

    // Getter and setter for $columnCharSymbol
    public function getColumnCharSymbol(): string
    {
        return $this->columnCharSymbol;
    }

    public function setColumnCharSymbol(string $columnCharSymbol): self
    {
        $this->columnCharSymbol = $columnCharSymbol;
        return $this;
    }

    // Getter and setter for $columnHeaderName
    public function getColumnHeaderName(): string
    {
        return $this->columnHeaderName;
    }

    public function setColumnHeaderName(string $columnHeaderName): self
    {
        $this->columnHeaderName = $columnHeaderName;
        return $this;
    }

    public function setCellDataValidation(CSVCellValidationDataTypeSetter $cellValidationSetter) : self
    {
        $this->cellValidationSetter = $cellValidationSetter;
        return $this;
    }
    public function getCellDataValidation() : ?CSVCellValidationDataTypeSetter
    {
        return $this->cellValidationSetter;
    }
    // Setter for $dataType
    // public function setDataType(string $dataType): self
    // {
    //     if(!in_array($dataType , $this->definedDataTypes))
    //     {
    //         throw new Exception("The selected " . $this->columnHeaderName . " column's data type " . $dataType . " is not defined !");
    //     }

    //     $this->dataType = $dataType;
    //     return $this;
    // }

    // // Getter for $dataType
    // public function getDataType(): string
    // {
    //     return $this->dataType;
    // }

    // public function defineValidValues(array $values) : self
    // {
    //     $this->validValues = $values;
    //     return $this;
    // }
    // public function getValidValues() : ?array
    // {
    //     return $this->validValues;
    // }
    public function setColumnWidth(int $width) : self
    {
        $this->width = $width;
        return $this;
    }
    public function getWidth() : ?int
    {
        return $this->width;
    }
}