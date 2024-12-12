<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\ValidationDataTypeSetters\CSVCellValidationDataTypeSetters\Traits;

use Exception;

trait CSVValidationDataTypeSettersSerilizing
{

    protected function getSerlizingProps() : array
    {
        return [  ];
    }

    protected function getSerlizingPropValues() : array
    {
        $values = [];
        foreach($this->getSerlizingProps() as $prop)
        {
            $values[$prop] = $this->{$prop};
        }
        return $values;
    }

    public function jsonSerialize(): mixed
    {
        return $this->getSerlizingPropValues();
    } 

    public function __serialize(): array
    {
        return $this->getSerlizingPropValues();
    }

    protected static function throwUnerilizableObjectException() : void
    {
        throw new Exception("Failed to unserlize CSVCellValidationDataTypeSetter ... A wrong Serilized data string is passed !");
    }
    
    protected static function DoesItHaveMissedSerlizedProps($data)
    { 
       return  ! is_array($data)  ;

    }
    
    protected static function checkRequiredProps($data) : void
    {
        if( static::DoesItHaveMissedSerlizedProps($data) )
        {
            static::throwUnerilizableObjectException();
        }
    }

    protected function setUnserlizedProps($data)
    { 
        static::checkRequiredProps($data); 
    } 

    // Rehydrate the object from serialized data
    public function __unserialize(array $data): void
    {
        $this->setUnserlizedProps($data);  
    }
     

}