<?php

namespace ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\Traits;

use ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\FormatColumnInfoComponents\CSVFormatColumnInfoComponentTypes\CSVDropDownListColumnInfoComponent;
use Illuminate\Support\Collection;

trait PublicsGetters
{

    public function getModelDbStoringValue(?string $userDisplayValue = null) : string|array|null
    {
        $component = $this->getModelColumnComponents()[$userDisplayValue] ?? null;
        if($component instanceof CSVDropDownListColumnInfoComponent)
        {
            return $component->getDbStoringValue($userDisplayValue);
        }

        return $userDisplayValue;
    }

    public function getModelDisplayValueReplacmentNeedingColumnFieldNames() : array
    {
        return array_map(function($component)
               {
                   return $component->getDatabaseFieldName();
               } , $this->getModelDisplayValueReplacmentNeedingColumns());
    }

    public function getModelDisplayValueReplacmentNeedingColumns() : array
    {
        return array_filter($this->getModelColumnComponents() , function($component)
               {
                   return $component instanceof CSVDropDownListColumnInfoComponent;
               });
    }

    public function getRelationshipDbStoringValue(string $relationName , ?string $userDisplayValue = null) : string|array|null
    {
        if( $relationComponents = $this->getRelationshipColumnComponents()[$relationName] ?? null )
        { 
            $component = $relationComponents[$userDisplayValue] ?? null;
            if($component instanceof CSVDropDownListColumnInfoComponent)
            {
                return $component->getDbStoringValue($userDisplayValue);
            }
        }

        return $userDisplayValue;
    }

    public function getRelationshipDisplayValueReplacmentNeedingColumnFieldNames(string $relationName) : array
    {
        return array_map(function($component)
               {
                   return $component->getDatabaseFieldName();
               } , $this->getRelationshipDisplayValueReplacmentNeedingColumns($relationName));
    }

    public function getRelationshipDisplayValueReplacmentNeedingColumns(string $relationName) : array
    {
        return array_filter($this->getRelationshipColumnComponents()[$relationName] , function($component)
               {
                  return $component instanceof CSVDropDownListColumnInfoComponent;
               });
    }

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