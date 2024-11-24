<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;


use Exception;

trait ImporterSerilizing
{

    protected function getSerlizingProps() : array
    {
        return [ 
              'ModelClass' , 'dataValidationRequestFormClass' , 'uploadedFileTempRealPath' , 'UploadedFileFullName'
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
               ! array_key_exists('ModelClass' , $data) ||
               ! array_key_exists('dataValidationRequestFormClass' , $data) ||
               ! array_key_exists('uploadedFileTempRealPath' , $data)  ||
               ! array_key_exists('UploadedFileFullName' , $data) ;
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

        $this->setModelClass($data["ModelClass"])
             ->setDataValidationRequestFormClass($data["dataValidationRequestFormClass"])
             ->setUploadedFileTempRealPath($data["uploadedFileTempRealPath"])
             ->setUploadedFileFullName($data["UploadedFileFullName"]);
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