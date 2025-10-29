<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponentTypes;
 
use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\ColumnDropDownListValue\ColumnDropDownListValueArrayHandler;
 
class CSVBooleanSelectingColumnInfo extends CSVDropDownListColumnInfoComponent
{ 
    protected ?array $valueOptions = null; 
    protected string $trueCaseDisplayValue ;
    protected string $falseCaseDisplayValue ;
 
    public function __construct(string $columnCharSymbol , string $columnHeaderName , string $trueCaseDisplayValue ,string $falseCaseDisplayValue )
    {
        parent::__construct($columnCharSymbol , $columnHeaderName , $this->getValueOptionArrayHandler($trueCaseDisplayValue , $falseCaseDisplayValue) );        
    }

    protected function composeValueOptionsArray(  string $trueCaseDisplayValue ,string $falseCaseDisplayValue) : array
    {
        return [ $trueCaseDisplayValue => 1  , $falseCaseDisplayValue => 0] ;
    }

    protected function initColumnDropDownListValueArrayHandler() : ColumnDropDownListValueArrayHandler
    {
        return ColumnDropDownListValueArrayHandler::create();
    }

    protected function getValueOptionArrayHandler( string $trueCaseDisplayValue ,string $falseCaseDisplayValue) : ColumnDropDownListValueArrayHandler
    {
        $handler = $this->initColumnDropDownListValueArrayHandler();
        $valueOptions = $this->composeValueOptionsArray($trueCaseDisplayValue , $falseCaseDisplayValue);
        return $handler->add_UserDisplay_DbValue_OptionsArray($valueOptions);
    }
 
 
}