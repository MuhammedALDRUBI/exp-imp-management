<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Throwable;

trait RelationshipImportingMethods
{
     
    protected function FailingHandlerRelationValidation(string $relationshipName, array $relationshipFillableValues = []) : bool
    {
        try
        {  
            
            $this->validateRelationshipSingleDataRow($relationshipName , $relationshipFillableValues );
            return true;

        }catch(Throwable $e)
        {

            $this->singleDataRowValidationFailed($this->currentDataRow , $e);
            return false; // to stop Data row importing execution after handling validation exception in 
        }
    }

    /**
     * Only HasOne relation is supported for CSVImporter
     */
    protected function initRelationObject(Model $model , $relationName) : HasOne | null
    {
        $relation = $model->{$relationName}();
        return $relation instanceof HasOne ? $relation : null;
    }

    
    protected function doesRelationColumnNeedUserDisplayValueReplacement(string $relationName , $columnFieldName) : bool
    {
        if($relationReplacementColumns = $this->relationColumnsNeedUserDisplayValueReplacement[$relationName] ?? null)
        {
            return in_array( $columnFieldName , $relationReplacementColumns);
        }
        return false;
    }

    public function getRelationshipDbStoringValue(string $relationName  , string $columnFieldName, ?string $userDisplayValue = null) : string|array|null
    {
        if($this->doesRelationColumnNeedUserDisplayValueReplacement($relationName , $columnFieldName))
        {
            return $this->getImportableFileFormatFactory()
                        ->getRelationshipDbStoringValue($relationName , $userDisplayValue);
        }

        return $userDisplayValue;
    }

    protected function getRelationshipFillableValues(string $relationshipName, array $relationshipFillables) : array
    {
        $dataValues = $this->currentDataRow[$relationshipName] ?? [];
        $fillables = [];

        foreach($relationshipFillables as $column)
        {
            if(isset($dataValues[$column]))
            {
                $initValue = $dataValues[$column] ?: null ; 
                
                $fillables[$column] = $this->getRelationshipDbStoringValue($relationshipName , $initValue);

            }

        }
        return $fillables;
    }

    protected function handleModelRelationship(Model $model , string $relationshipName , array $relationshipFillables) : void
    {
        $relationshipFillableValues = $this->getRelationshipFillableValues($relationshipName , $relationshipFillables);

        if(!empty($relationshipFillableValues))
        {
            if($relation = $this->initRelationObject($model , $relationshipName)) // checking relation type before doing any operation
            {
                $this->FailingHandlerRelationValidation( $relationshipName,  $relationshipFillableValues );
                $relation->create($relationshipFillableValues); //if any exception is thrown the database transaction will be failed in the parent Importer
            }
        }
    }

    protected function setRelationsetColumnsNeedUserDisplayValueReplacement() : self
    {
        foreach(array_keys($this->getRelationshipsFillableColumns()) as $relationshipName)
        {
            $this->relationColumnsNeedUserDisplayValueReplacement[$relationshipName] 
            =
            $this->getImportableFileFormatFactory()
                 ->getRelationshipDisplayValueReplacmentNeedingColumnFieldNames($relationshipName);
        }
        return $this;
    }

    protected function setRelationshipsFillableColumns() : void
    {
        $this->relationshipsFillables = $this->getImportableFileFormatFactory()->getRelationshipsDataBaseFields();
    }

    protected function getRelationshipsFillableColumns() : array
    {
        return $this->relationshipsFillables;
    }
    
    public function doesItHaveRelationships() : bool
    {
        return $this->getImportableFileFormatFactory()->doesItHaveRelationships();
    }

    protected function handleModelRelationships(Model $model) : void
    {
        if($this->doesItHaveRelationships())
        {
            $this->setRelationshipsFillableColumns();
            $this->setRelationsetColumnsNeedUserDisplayValueReplacement();

            foreach($this->getRelationshipsFillableColumns() as $relationshipName => $relationshipFillables)
            {
                $this->handleModelRelationship($model , $relationshipName , $relationshipFillables);
            }
        }
    }

}