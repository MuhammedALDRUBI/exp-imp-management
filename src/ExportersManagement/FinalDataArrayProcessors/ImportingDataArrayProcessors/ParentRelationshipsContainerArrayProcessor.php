<?php

namespace ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\ImportingDataArrayProcessors;


use ExpImpManagement\ExportersManagement\FinalDataArrayProcessors\DataArrayProcessor;
use Illuminate\Database\Eloquent\Model;


//Handling Relationships the model belongs to
class ParentRelationshipsContainerArrayProcessor extends DataArrayProcessor
{
    protected function processModelSingleDesiredColumns(string $column , Model $model ,array $row = []) : array
    {
        $row[ $column ] =  $this->getObjectKeyValue($column, $model );
        return $row;
    }
}
