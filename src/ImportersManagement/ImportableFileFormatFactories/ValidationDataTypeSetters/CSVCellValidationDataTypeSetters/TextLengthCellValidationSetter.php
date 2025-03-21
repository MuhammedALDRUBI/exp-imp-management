<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;


use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class TextLengthCellValidationSetter extends CSVCellValidationDataTypeSetter
{

    protected int $maxLength ;
    protected int $minLength = 0;
    public function __construct(int $maxLength , int $minLength = 0)
    {
        $this->setMaxLength($maxLength);
        $this->seMinLength($minLength);
    }

    protected function setMaxLength(string $maxLength ) 
    {
        $this->maxLength = $maxLength;
    }
    
    protected function seMinLength(string $minLength ) 
    {
        $this->minLength = $minLength;
    }

    protected function getSerlizingProps() : array
    {
        return [ 'maxLength' , 'minLength' ];
    }

    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) ||
               !array_key_exists("maxLength" , $data) ||
               !array_key_exists("minLength" , $data) ;
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data);
        $this->setMaxLength($data["maxLength"]);
        $this->seMinLength($data["minLength"]); 
    }

    public function setCellDataValidation(DataValidation $dataValidation)
    {
        $dataValidation->setType( DataValidation::TYPE_TEXTLENGTH );
        $dataValidation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_BETWEEN);
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setError('Value Length Must be in range  ' . $this->minLength . ' - ' . $this->maxLength);
        $dataValidation->setPromptTitle('Enter a string with length in range ' . $this->minLength . ' - ' . $this->maxLength);
        $dataValidation->setPrompt('Enter a string with length in range ' . $this->minLength . ' - ' . $this->maxLength);
        $dataValidation->setFormula1($this->minLength); // Minimum length
        $dataValidation->setFormula2($this->maxLength); // Maximum length
    }
}