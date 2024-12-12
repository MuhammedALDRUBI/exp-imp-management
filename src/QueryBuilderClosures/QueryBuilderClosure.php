<?php

namespace ExpImpManagement\QueryBuilderClosures;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as DatabaseQueryBuilder;
use JsonSerializable;
use Spatie\QueryBuilder\QueryBuilder;

abstract class QueryBuilderClosure implements JsonSerializable
{
    abstract public function callOnBuilder(QueryBuilder | Builder | DatabaseQueryBuilder $queryBuilder);

    public function jsonSerialize(): mixed
    {
        return [];
    }  
}