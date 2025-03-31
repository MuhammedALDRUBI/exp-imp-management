<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\ColumnDropDownListValue;


class ColumnDropDownListValue
{
    protected string $dbStoringValue ;
    protected string $userDisplayValue ;

    public function __construct(string $dbStoringValue , ?string $userDisplayValue = null)
    {
        $this->dbStoringValue = $dbStoringValue;
        $this->setUserDisplayValue($dbStoringValue , $userDisplayValue);
    }

    public static function create(string $dbStoringValue , ?string $userDisplayValue = null) : self
    {
        return new static($dbStoringValue , $userDisplayValue);
    }

    protected function setUserDisplayValue( string $dbStoringValue , ?string $userDisplayValue = null )
    {
        $this->userDisplayValue = $userDisplayValue ?? $dbStoringValue;
    }

    public function getDbStoringValue() : string
    {
        return $this->dbStoringValue;
    }

    public function getUserDisplayValue() : string
    {
        return $this->userDisplayValue;
    }
 
}