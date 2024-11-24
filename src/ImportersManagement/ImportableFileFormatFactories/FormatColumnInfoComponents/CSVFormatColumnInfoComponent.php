<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents;
 
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\CSVCellValidationDataTypeSetter;

class CSVFormatColumnInfoComponent extends FormatColumnInfoComponent
{
     
    protected string $columnCharSymbol;
    protected string $columnHeaderName; 
    protected ?int $width  = null;
    protected ?CSVCellValidationDataTypeSetter $cellValidationSetter = null;
 
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