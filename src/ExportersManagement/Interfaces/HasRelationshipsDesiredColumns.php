<?php

namespace ExpImpManagement\ExportersManagement\Interfaces;


interface HasRelationshipsDesiredColumns
{
    public function setRelationshipsDefaultDesiredFinalColumnsArray( array $RelationshipsDesiredFinalColumns = []): HasRelationshipsDesiredColumns;
}
