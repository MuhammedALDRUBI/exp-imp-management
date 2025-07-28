<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\ColumnDropDownListValue;


class ColumnDropDownListValueArrayHandler
{
    protected array $optionValues = [];

    public static function create() : self
    {
        return new static();
    } 

    public function addColumnDropDownListValue(string $dbStoringValue , ?string $userDisplayValue = null) : self
    {
        if(!$userDisplayValue)
        {
            $userDisplayValue = $dbStoringValue;
        }

        $this->optionValues[$userDisplayValue] = $dbStoringValue;

        return $this;
    }

    public function handleColumnDropDownListValue(ColumnDropDownListValue $value) : self
    {
        return $this->addColumnDropDownListValue(
                                                    $value->getUserDisplayValue(),
                                                    $value->getDbStoringValue()
                                                );
    }

    public function add_UserDisplay_DbValue_OptionsArray(array $options) : self
    {
        foreach($options as $dbStoringValue => $userDisplayValue)
        { 
            $this->addColumnDropDownListValue($dbStoringValue , $userDisplayValue);
        }
        return $this;
    }

    public function addDBValueIndexedOptionsArray(array $options) : self
    {
        foreach($options as $dbStoringValue)
        { 
            $this->addColumnDropDownListValue($dbStoringValue);
        }
        return $this;
    }

    public function getFinalArray() : array
    {
        return $this->optionValues;
    }
}