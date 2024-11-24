<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits;

use Exception;

trait CSVImportableFileFormatFactorySerilizing
{

    protected function getSerlizingProps() : array
    {
        return [ 
              'fileName'  , 'headers', 'formatDataCollection'  
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
        throw new Exception("Failed to unserlize Importer ... A wrong Serilized data string is passed !");
    }
    
    protected static function DoesItHaveMissedSerlizedProps($data)
    {
       return  ! is_array($data) ||
               ! array_key_exists('fileName' , $data) ||
               ! array_key_exists('headers' , $data) ||
               ! array_key_exists('formatDataCollection' , $data)   ;
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

        $this->setFileName($data["fileName"])
             ->setHeaders($data["headers"])
             ->setDataFileToManuallyChange($data["formatDataCollection"]);
    } 

    // Rehydrate the object from serialized data
    public function __unserialize(array $data): void
    {
        $this->setUnserlizedProps($data);  
        $this->__wakeup(); 
    }
    
    public function __wakeup()
    {
        $this->setValidColumnFormatInfoCompoenents();
    }

}