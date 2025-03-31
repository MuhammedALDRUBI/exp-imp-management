<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponentTypes;

use Exception;
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\ListCellValidationSetter;
 

class CSVMultipleSelectionDropDownListColumnInfo extends CSVDropDownListColumnInfoComponent
{  
    protected function initListCellValidationSetter() : ListCellValidationSetter
    {
        return parent::initListCellValidationSetter()->enableMultipleSelection();
    }
   
    public function getDbStoringValue(string $userDisplayValue) : string|array
    {
        $dbStoringValues = [];
        foreach(explode("," , $userDisplayValue) as $value)
        {
            $dbStoringValues[] = $this->valueOptions[$value] 
                                 ??
                                 throw new Exception("The selected " . $userDisplayValue . " is invalid value");
        }
        return $dbStoringValues;
    }
   
}