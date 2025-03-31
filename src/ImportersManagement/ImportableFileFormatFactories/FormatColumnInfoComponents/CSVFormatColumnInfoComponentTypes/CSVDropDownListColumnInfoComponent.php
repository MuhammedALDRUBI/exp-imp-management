<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponentTypes;

use Exception;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\ColumnDropDownListValue\ColumnDropDownListValueArrayHandler;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponent;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\CSVCellValidationDataTypeSetter;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\ListCellValidationSetter;
 

/**
 * @property ListCellValidationSetter $cellValidationSetter
 */
class CSVDropDownListColumnInfoComponent extends CSVFormatColumnInfoComponent
{ 
    protected ?array $valueOptions = null; 
 
    public function __construct(string $columnCharSymbol , string $columnHeaderName , ColumnDropDownListValueArrayHandler $valuesHandler )
    {
        parent::__construct($columnCharSymbol , $columnHeaderName);
        $this->setValueOptions($valuesHandler)->handleCellDataValidation();
    }

    protected function initListCellValidationSetter() : ListCellValidationSetter
    {
        $userDisplays = array_keys($this->valueOptions);
        return new ListCellValidationSetter($userDisplays);
    }

    protected function handleCellDataValidation() : void
    {
        $this->setCellDataValidation($this->initListCellValidationSetter());
    }

    public function setCellDataValidation(?CSVCellValidationDataTypeSetter $cellValidationSetter = null) : self
    {
        if(!$cellValidationSetter instanceof ListCellValidationSetter)
        {
            throw new Exception("The passed cell data validation setter is not an child type of " . ListCellValidationSetter::class);
        }

        return parent::setCellDataValidation($cellValidationSetter);
    }

    protected function unserilizeValueOptions(array $array) : self
    {
        $this->valueOptions = $array;
        return $this;
    }

    public function setValueOptions(ColumnDropDownListValueArrayHandler $valuesHandler) : self
    {
        $this->valueOptions = $valuesHandler->getFinalArray();
        return $this;
    }

    public function getDbStoringValue(string $userDisplayValue) : string|array
    {
        return $this->valueOptions[$userDisplayValue] ??
               throw new Exception("The selected " . $userDisplayValue . " is invalid value");
    }

    protected function getSerlizingProps() : array
    {
        $props = parent::getSerlizingProps();
        $props[] = "valueOptions";
        return $props;
    }


    
    protected static function DoesItHaveMissedSerlizedProps($data)
    { 
        return parent::DoesItHaveMissedSerlizedProps($data) 
               ||
               !array_key_exists('valueOptions' , $data);
    }
     

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data);

        $this->unserilizeValueOptions($data["valueOptions"]);  
    } 

    public function DoesSupportMultipleSelection() : bool
    {
        return $this->cellValidationSetter->DoesSupportMultipleSelection();
    }
}