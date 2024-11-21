<?php

namespace ExpImpManagement\ExportersManagement\Exporter\Traits;

use Exception;

trait ExporterSerilizing
{

    protected function getSerlizingProps() : array
    {
        return [ 
            'fileName' , 'fileFullName'  , 'ModelClass' , 'DataCollection' 
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
        throw new Exception("Failed to unserlize Exporter ... A wrong Serilized data string is passed !");
    }
    
    protected static function DoesItHaveMissedSerlizedProps($data)
    {
       return  ! is_array($data) ||
               ! array_key_exists('fileName' , $data) ||
               ! array_key_exists('fileFullName' , $data) ||
               ! array_key_exists('ModelClass' , $data)  ||
               ! array_key_exists('DataCollection' , $data) ;
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

        $this->setFileName($data["fileName"])->setFileFullName()
             ->setModelClass($data["ModelClass"])
             ->useDataCollection($data["DataCollection"]);
    } 

    // Rehydrate the object from serialized data
    public function __unserialize(array $data): void
    {
        $this->setUnserlizedProps($data); 
        
        if(method_exists($this , '__wakeup'))
        {
            $this->__wakeup();
        }
    }

}