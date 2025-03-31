<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters;

use Dotenv\Repository\Adapter\MultiReader;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation; 

class ListCellValidationSetter extends CSVCellValidationDataTypeSetter
{
    protected array $options = [];
    protected bool $multipleSelection = false;

    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    public function enableMultipleSelection() : self
    {
        $this->multipleSelection = true;
        return $this;
    }

    protected function unserlizeMultipleSelection(bool $value) : self
    {
        $this->multipleSelection = $value;
        return $this;
    }

    public function DoesSupportMultipleSelection() : bool
    {
        return $this->multipleSelection;
    }
    
    public function setOptions(array $options )
    {
        $this->options = $options;
    }

    protected function getSerlizingProps() : array
    {
        return [ 'options'  , 'multipleSelection'];
    }

    protected static function DoesItHaveMissedSerlizedProps($data)
    {
        return parent::DoesItHaveMissedSerlizedProps($data) ||
               !array_key_exists("options" , $data)   ||
               !array_key_exists("multipleSelection" , $data)  ;
    }

    protected function setUnserlizedProps($data)
    { 
        parent::setUnserlizedProps($data); 
        $this->setOptions($data["options"]); 
        $this->unserlizeMultipleSelection($data["multipleSelection"]);
    }


    public function setCellDataValidation(DataValidation $dataValidation)
    {
        $dataValidation->setType(DataValidation::TYPE_LIST);
        $dataValidation->setError('Value is not in list.');
        $dataValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);

        //for allowing user to select mult values sperated with commas
        $dataValidation->setAllowBlank($this->multipleSelection);

        $dataValidation->setShowInputMessage(true);
        $dataValidation->setShowErrorMessage(true);
        $dataValidation->setErrorTitle('Input error');
        $dataValidation->setShowDropDown(true);
        $dataValidation->setPromptTitle('Pick from list');
        $dataValidation->setPrompt('Please pick a value from the drop-down list.');
        $dataValidation->setFormula1(sprintf('"%s"', implode(',', $this->options)));
    }
}