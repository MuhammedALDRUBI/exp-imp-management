<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

abstract class CSVCellValidationDataTypeSetter
{

    abstract public function setCellDataValidation(DataValidation $dataValidation);
    
}