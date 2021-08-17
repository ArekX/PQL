<?php

namespace ArekX\PQL\Drivers\MySQL\Traits;

use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Raw;

trait BuildStructuredQuery
{
    use BuildParentQuery;

    protected function buildStructuredQuery(StructuredQuery $query, QueryBuilderState $state): string
    {
        $result = $this->buildQueryByParent($query, $state);

        if ($query instanceof Raw) {
            return $result->getQuery();
        }

        return "(" . $result->getQuery() . ")";
    }
}