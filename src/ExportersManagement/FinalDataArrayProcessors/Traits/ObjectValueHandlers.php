<?php

namespace ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ObjectValueHandlers
{

    protected function showModelAllHiddenAttributes(Model $model) : Model
    {
        return $model->makeVisible($model->getHidden());
    }

    protected function getModelAllKeysArray(  Model $model) : array | null
    {
        return array_keys(
            $this->showModelAllHiddenAttributes($model)->toArray()
        );
    }

    protected function getModelRelationshipsAttributesKeysArray(Model $model) : array
    {
        return array_keys(
            $this->showModelAllHiddenAttributes($model)->relationsToArray()
        );
    }

    protected function getModelAttributesKeysArray(Model $model) : array
    {
        return array_keys(
            $this->showModelAllHiddenAttributes($model)->attributesToArray()
        );
    }

    protected function getCollectionModel(Collection | Model | null $modelOrCollection = null) :  Model | null
    {
        if(!$modelOrCollection){return null;}
        if($modelOrCollection instanceof Collection) {$modelOrCollection = $modelOrCollection->first();}
        return $modelOrCollection;
    }

    protected function getModelOrCollectionAllAttributesKeysArray(Collection | Model | null $modelOrCollection = null) : array | null
    {
        return $this->getModelAllKeysArray(
            $this->getCollectionModel($modelOrCollection)
        );
    }

    protected function getModelOrCollectionAttributesKeysArray(Collection | Model | null $modelOrCollection = null) : array | null
    {
        return $this->getModelAttributesKeysArray(
                    $this->getCollectionModel($modelOrCollection)
                );
    }
    
     
    /**
     * @param array $keys
     * @param Model|Collection|array|null $object
     * @return array|null
     * Used To get Associative Array
     */
    protected function getObjectKeysValues(array $keys ,  Model | Collection | array | null $object = null) : array | null
    {
        if(!$object ){return null;}
        if(is_array($object))
        {
            /** * @var array $object */
            return array_intersect_key($object, array_flip($keys));
        }

        if($object instanceof Model)
        {
            /** * @var Model | Collection $object */
            return $object->only($keys);
        }

        return $object->map(function ($row) use ( $keys)
        {
            return $row->only($keys);
        })->toArray();
    }

    /**
     * @param string|array $keyName
     * @param Model|Collection|array|null $object
     * @return string|null
     * USed To get String value
     */
    protected function getObjectKeyValue(string | array $keyName , Model | Collection | array | null $object) : string | null
    {
        if(!$object ){return null;}
        if($object instanceof Model) { return  $object->{$keyName}; }

        //If Object is An array
        if(is_array($object)) { return $object[$keyName] ?? null; }

        //If Object Is a Collection
        return $object->map(function ($row) use ($keyName)
        {
            if($row->{$keyName}){return $row->{$keyName};}
        })->join(" , ");
    }

}
