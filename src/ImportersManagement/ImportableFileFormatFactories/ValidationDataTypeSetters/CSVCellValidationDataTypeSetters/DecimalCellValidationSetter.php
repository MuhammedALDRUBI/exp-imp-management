<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class DecimalCellValidationSetter extends CSVCellValidationDataTypeSetter
{

    public function setCellDataValidation(DataValidation $dataValidation)
    {
        $dataValidation->setType( DataValidation::TYPE_DECIMAL );
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setOperator(DataValidation::OPERATOR_GREATERTHAN); // Ensure the value is greater than 0
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setError('The value must be a decimal greater than 0.');
        $dataValidation->setPromptTitle('Enter a decimal number');
        $dataValidation->setPrompt('Enter a decimal number');
        $dataValidation->setFormula1(0); // Minimum length
    }
}