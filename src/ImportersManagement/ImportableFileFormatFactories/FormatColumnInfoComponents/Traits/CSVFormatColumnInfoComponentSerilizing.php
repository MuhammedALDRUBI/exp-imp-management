<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\Traits;

use Exception;

trait CSVFormatColumnInfoComponentSerilizing
{

    protected function getSerlizingProps() : array
    {
        return [ 
            'columnCharSymbol' , 'columnHeaderName' , 'databaseFieldName' , 'relationName' , 'prefixingColumnNameStatus' , 'columnHeaderPrefix' , 'width' , 'cellValidationSetter'
        ];
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
        throw new Exception("Failed to unserlize CSVFormatColumnInfoComponent ... A wrong Serilized data string is passed !");
    }
    
    protected static function DoesItHaveMissedSerlizedProps($data)
    { 
       return  ! is_array($data) ||
               ! array_key_exists('columnCharSymbol' , $data) || 
               ! array_key_exists('columnHeaderName' , $data) || 
               ! array_key_exists('databaseFieldName' , $data) || 
               ! array_key_exists('relationName' , $data) ||  
               ! array_key_exists('columnHeaderPrefix' , $data) ||  
               ! array_key_exists('width' , $data) ||
               ! array_key_exists('cellValidationSetter' , $data) ||
               !array_key_exists("prefixingColumnNameStatus" , $data);

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

        $this->setColumnCharSymbol($data["columnCharSymbol"])
             ->setColumnHeaderName($data["columnHeaderName"])
             ->setDatabaseFieldName($data["databaseFieldName"])
             ->setColumnWidth($data["width"]) 
             ->setCellDataValidation($data["cellValidationSetter"]);

             if($relationName = $data["relationName"])
             {
                $this->relationshipColumn($relationName ,  $data["prefixingColumnNameStatus"]  , $data["columnHeaderPrefix"]) ;
             }

    } 

    // Rehydrate the object from serialized data
    public function __unserialize(array $data): void
    {
        $this->setUnserlizedProps($data);  
    }
     

}