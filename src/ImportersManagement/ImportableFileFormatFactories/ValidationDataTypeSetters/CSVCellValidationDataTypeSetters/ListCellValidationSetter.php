<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;


use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class ListCellValidationSetter extends CSVCellValidationDataTypeSetter
{
    protected array $options = [];

    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    public function setOptions(array $options )
    {
        $this->options = $options;
    }

    protected function getSerlizingProps() : array
    {
        return [ 'options'  ];
    }

    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) ||
               !array_key_exists("options" , $data)   ;
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data); 
        $this->setOptions($data["options"]); 
    }


    public function setCellDataValidation(DataValidation $dataValidation)
    {
        $dataValidation->setType(DataValidation::TYPE_LIST);
        $dataValidation->setError('Value is not in list.');
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $dataValidation->setAllowBlank(false);
        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setShowDropDown(true);
        $dataValidation->setPromptTitle('Pick from list');
        $dataValidation->setPrompt('Please pick a value from the drop-down list.');
        $dataValidation->setFormula1(sprintf('"%s"', implode(',', $this->options)));
    }
}