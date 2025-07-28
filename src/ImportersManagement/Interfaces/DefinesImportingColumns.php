<?php

namespace ExpImpManagement\ImportersManagement\Interfaces;


interface DefinesImportingColumns
{

    /**
     * 
     * must return an array like :
     *  ["id" , "name" , "phone" , "created_at" , "deleted_at" , "updated_at"] 
     */
    public function getImportingColumns() :  array;
}