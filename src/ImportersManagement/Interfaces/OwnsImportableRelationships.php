<?php

namespace ExpImpManagement\ImportersManagement\Interfaces;


interface OwnsImportableOneToOneRelationships
{

    /**
     * 
     * must return an array like :
     * ["info" => ["name" , "phone"] , "profile" => ]
     */
    public function getImportableOneToOneRelationships() :  array
}