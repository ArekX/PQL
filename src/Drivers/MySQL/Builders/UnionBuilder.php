<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;

class UnionBuilder implements QueryBuilderChild
{
   protected QueryBuilder $parent;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $results = [
            $this->buildQuery($structure['initial'], $state)
        ];

        foreach ($structure['union'] as [$type, $query]) {
            $results[] = $this->buildUnion($type, $query, $state);
        }

        return RawSqlQuery::create(implode(PHP_EOL, $results), $state->getQueryParams()->getParams());
    }

    protected function buildUnion(string $type, StructuredQuery $query, QueryBuilderState $state)
    {
        return strtoupper($type) . ' ' . $this->buildQuery($query, $state);
    }

    protected function buildQuery(StructuredQuery $query, QueryBuilderState $state): string
    {
        return $this->parent->build($query, $state)->getQuery();
    }

    public function setParent(QueryBuilder $parent)
    {
        $this->parent = $parent;
    }
}