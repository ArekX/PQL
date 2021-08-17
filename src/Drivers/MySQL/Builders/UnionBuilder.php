<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildParentQuery;

class UnionBuilder implements QueryBuilderChild
{
    use BuildParentQuery;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $results = [
            $this->buildQueryByParent($structure['initial'], $state)->getQuery()
        ];

        foreach ($structure['union'] as [$type, $query]) {
            $results[] = $this->buildUnion($type, $query, $state);
        }

        return RawSqlQuery::create(
            implode($state->get('queryGlue'), $results),
            $state->getQueryParams()->getParams()
        );
    }

    protected function buildUnion(string $type, StructuredQuery $query, QueryBuilderState $state)
    {
        return strtoupper($type) . ' ' . $this->buildQueryByParent($query, $state)->getQuery();
    }
}