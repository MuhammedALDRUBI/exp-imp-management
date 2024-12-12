<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits;

use Illuminate\Support\Collection;

trait PublicsGetters
{
    
    public function doesItHaveRelationships() : bool
    {
        return !empty( $this->getRelationshipColumnComponents() );
    }

    public function getRelationshipsDataBaseFields() : array
    {
        $fileds = [];
        foreach($this->relationshipsColumnComponents as $relationName => $relationshipColumnComponents)
        {
            $fileds[$relationName] = array_map(function($component)
                                                {
                                                    return $component->getDatabaseFieldName();
                                                } , $relationshipColumnComponents);
        }

        return $fileds;
    }

    public function getRelationshipDatabaseFields(string $relationName) : array
    {
        $relationshipComponents = $this->relationshipsColumnComponents[$relationName] ?? [];
        return array_map(function($component)
                {
                    return $component->getDatabaseFieldName();
                } , $relationshipComponents);

    }

    public function getReationshipsHeadings() : array
    {
        $headings = [];
        foreach($this->relationshipsColumnComponents as $relationName => $relationshipColumnComponents)
        {
            $headings[$relationName] = array_map(function($component)
                                                {
                                                    return $component->getColumnHeaderName();
                                                } , $relationshipColumnComponents);
        }

        return $headings;
    }
    
    public function getRelationshipHeadings(string $relationName)
    {
        $relationshipComponents = $this->relationshipsColumnComponents[$relationName] ?? [];
        return array_map(function($component)
               {
                    return $component->getColumnHeaderName();
               } , $relationshipComponents);
    }

    public function getRelationshipNames()  :array
    {
        return array_keys( $this->getRelationshipColumnComponents() );
    }

    public function getRelationshipColumnComponents() 
    {
        return $this->relationshipsColumnComponents ;
    }


    public function getModelDatabaseFields() : array
    {
        return $this->getModelHeadings();
    }

    public function getModelHeadings() : array
    {
        return array_map(function($component)
                {
                    return $component->getColumnHeaderName();
                },$this->modelColumnComponents);
    }
    public function getModelColumnComponents() : array
    {
        return $this->modelColumnComponents;
    }

    
    protected function getValidSortedData(Collection $data) : Collection
    {
        //must return the values by sorting its keys based on headings sorting style
        $headings = $this->getHeadingsKeysArray();

        return $data->map(function ($item) use ($headings) 
                   {
                        // Filter keys based on headings and reorder them
                        return  array_merge(
                                                            $headings, // Create an array with headings as keys to preserve order
                                                            array_intersect_key($item, $headings) // Filter keys
                                                        );
                   });
    }

}