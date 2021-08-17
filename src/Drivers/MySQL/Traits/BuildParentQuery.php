<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;

trait BuildParentQuery
{
    protected QueryBuilder $parent;

    public function setParent(QueryBuilder $parent)
    {
        $this->parent = $parent;
    }

    protected function buildQueryByParent(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        return $this->parent->build($query, $state);
    }
}