<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;


use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class TimeCellValidationSetter extends CSVCellValidationDataTypeSetter
{
    protected string $startTime ;
    protected string $endTime ;

    public function __construct(string $startTime , string $endTime )
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
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