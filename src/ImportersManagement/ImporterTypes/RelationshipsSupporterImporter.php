<?php

namespace ExpImpManagement\ImportersManagement\ImporterTypes;

use ExpImpManagement\ImportersManagement\Importer\Importer;
use Illuminate\Database\Eloquent\Model;

abstract class RelationshipsSupporterImporter extends Importer
{

    protected function getDataFileExpectedExtension(): string
    {
        return "json";
    }

    abstract protected function getDesiredRelationships() : array;

    protected function importRelationships(Model $model , array $dataRow):Importer
    {
        return $this;
    }

    protected function importDataRow(array $row): Importer
    {
        return $this->importRelationships(
            parent::importDataRow($row) ,
            $row
        );
    }



}
