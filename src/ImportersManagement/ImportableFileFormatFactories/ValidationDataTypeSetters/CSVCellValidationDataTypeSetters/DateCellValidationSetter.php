<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;


use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class DateCellValidationSetter extends CSVCellValidationDataTypeSetter
{

    protected string $startDate ;
    protected string $endDate ;

    public function __construct(string $startDate , string $endDate )
    {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate); 
    }

    protected function setStartDate(string $startDate ) 
    {
        $this->startDate = $startDate;
    }
    
    protected function setEndDate(string $endDate ) 
    {
        $this->endDate = $endDate;
    }
    protected function getSerlizingProps() : array
    {
        return [ 'startDate' , 'endDate' ];
    }

    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) ||
               !array_key_exists("startDate" , $data) ||
               !array_key_exists("endDate" , $data) ;
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data);
        $this->setStartDate($data["startDate"]);
        $this->setEndDate($data["endDate"]); 
    }

    public function setCellDataValidation(DataValidation $dataValidation)
    {
        $dataValidation->setType( DataValidation::TYPE_DATE );
        $dataValidation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_BETWEEN);
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setError('Value Must a date between ' . $this->startDate . ' and ' . $this->endDate);
        $dataValidation->setPromptTitle('Enter a date between ' . $this->startDate . ' and ' . $this->endDate);
        $dataValidation->setPrompt('Enter a date between ' . $this->startDate . ' and ' . $this->endDate);
        $dataValidation->setFormula1($this->startDate); 
        $dataValidation->setFormula2($this->endDate); 
    }
}