<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\Traits\CSVValidationDataTypeSettersSerilizing;
use JsonSerializable;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

abstract class CSVCellValidationDataTypeSetter implements JsonSerializable
{
 
    use CSVValidationDataTypeSettersSerilizing;

    abstract public function setCellDataValidation(DataValidation $dataValidation);
    
}