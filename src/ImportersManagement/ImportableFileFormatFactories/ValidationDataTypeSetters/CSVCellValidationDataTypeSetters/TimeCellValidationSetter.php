<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;


use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class TimeCellValidationSetter extends CSVCellValidationDataTypeSetter
{
    protected string $startTime ;
    protected string $endTime ;

    public function __construct(string $startTime , string $endTime )
    {
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);  
    }

    protected function setStartTime(string $startTime ) 
    {
        $this->startTime = $startTime;
    }
    
    protected function setEndTime(string $endTime ) 
    {
        $this->endTime = $endTime;
    }
    protected function getSerlizingProps() : array
    {
        return [ 'startTime' , 'endTime' ];
    }

    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) ||
               !array_key_exists("startTime" , $data) ||
               !array_key_exists("endTime" , $data) ;
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data);
        $this->setStartTime($data["startTime"]);
        $this->setEndTime($data["endTime"]); 
    }
    
    public function setCellDataValidation(DataValidation $dataValidation)
    {
        $dataValidation->setType( DataValidation::TYPE_TIME );
        $dataValidation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_BETWEEN);
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setError('Value Must a time between ' . $this->startTime . ' and ' . $this->endTime);
        $dataValidation->setPromptTitle('Enter a time between ' . $this->startTime . ' and ' . $this->endTime);
        $dataValidation->setPrompt('Enter a time between ' . $this->startTime . ' and ' . $this->endTime);
        $dataValidation->setFormula1($this->startTime); 
        $dataValidation->setFormula2($this->endTime); 
    }
}