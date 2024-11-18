<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;


use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class DateCellValidationSetter extends CSVCellValidationDataTypeSetter
{

    protected string $startDate ;
    protected string $endDate ;

    public function __construct(string $startDate , string $endDate )
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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