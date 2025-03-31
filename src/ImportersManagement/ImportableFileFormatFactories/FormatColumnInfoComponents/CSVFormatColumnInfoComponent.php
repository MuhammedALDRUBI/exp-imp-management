<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\Traits\CSVFormatColumnInfoComponentSerilizing;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\CSVCellValidationDataTypeSetter;
use Illuminate\Support\Str;
use JsonSerializable;

class CSVFormatColumnInfoComponent extends FormatColumnInfoComponent implements JsonSerializable
{
     use CSVFormatColumnInfoComponentSerilizing;

    protected string $columnCharSymbol;
    protected string $columnHeaderName; 
    protected string $databaseFieldName ; 
    protected ?string $relationName = null;
    protected bool $prefixingColumnNameStatus = true;
    protected ?string $columnHeaderPrefix = null;
    protected ?int $width  = null;
    protected ?CSVCellValidationDataTypeSetter $cellValidationSetter = null;
 
    public function __construct(string $columnCharSymbol , string $columnHeaderName )
    {
        $this->setColumnCharSymbol($columnCharSymbol)
             ->setColumnHeaderName($columnHeaderName) 
             ->setDatabaseFieldName( $columnHeaderName);
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

    // Getter and setter for $databaseFieldName
    public function getDatabaseFieldName(): string
    {
        return $this->databaseFieldName;
    }

    public function setDatabaseFieldName(string $databaseFieldName): self
    {
        $this->databaseFieldName = $databaseFieldName;
        return $this;
    }

    protected function setColumnHeaderPrefix(?string $columnHeaderPrefix) : self
    {
        if(!$columnHeaderPrefix)
        {
            $columnHeaderPrefix = $this->getRelationName();
        }

        $this->columnHeaderPrefix = $columnHeaderPrefix;

        return $this;
    }

    public function getColumnHeaderPrefix() : ?string
    {
        return $this->columnHeaderPrefix ;
    }

    protected function setPrefixingColumnNameStatus(bool $prefixingColumnNameStatus = true) : self
    {
        $this->prefixingColumnNameStatus = $prefixingColumnNameStatus;
        return $this;
    }

    protected function getPrefixingColumnNameStatus() : bool
    {
        return $this->prefixingColumnNameStatus ;
    }

    /**
     * Setter needed for serilizing
     */
    protected function setRelationName(?string $relationName) : self
    {
        $this->relationName = $relationName;
        return $this;
    }
    public function getRelationName()  : ?string
    {
        return $this->relationName;
    }

    public function isItRelationColumn() : bool
    {
        return (bool) $this->getRelationName() ;
    }


    /** 
     * Once $columnHeaderPrefix is null the $relationName value will be used as a column prefix as long as $prefixingColumnName value is true
    */
    public function relationshipColumn(string $relationName , bool $prefixingColumnName = true , ?string $columnHeaderPrefix = null) : self
    {
        $this->setRelationName($relationName);
        $this->setPrefixingColumnNameStatus($prefixingColumnName);
        $this->setColumnHeaderPrefix($columnHeaderPrefix);
        $this->handlePrefixedHeaderName();
        return $this;
    }

    // Getter and setter for $columnHeaderName
    public function getColumnHeaderName(): string
    {
        return $this->columnHeaderName;
    }

    protected function getPrefiexedHeaderName() : string
    {
        if($this->getColumnHeaderPrefix() )
        {
            return Str::ucfirst( $this->getColumnHeaderPrefix() ) . " " . $this->getColumnHeaderName()  ;
        } 
        
        return  $this->getColumnHeaderName()  ;
    }

    protected function handlePrefixedHeaderName() : void
    {
        if($this->getPrefixingColumnNameStatus() && $this->getColumnHeaderPrefix())
        {
            $this->setColumnHeaderName( $this->getPrefiexedHeaderName() );
        }
    }

    public function setColumnHeaderName(string $columnHeaderName): self
    {
        $this->columnHeaderName = $columnHeaderName;
        return $this;
    }

    public function setCellDataValidation(?CSVCellValidationDataTypeSetter $cellValidationSetter = null) : self
    {
        $this->cellValidationSetter = $cellValidationSetter;
        return $this;
    }

    public function getCellDataValidation() : ?CSVCellValidationDataTypeSetter
    {
        return $this->cellValidationSetter;
    } 

    public function setColumnWidth(?int $width) : self
    {
        $this->width = $width;
        return $this;
    }

    public function getWidth() : ?int
    {
        return $this->width;
    }
}