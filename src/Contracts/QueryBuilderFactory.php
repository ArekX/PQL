<?php

namespace ArekX\PQL\Contracts;

/**
 * Interface which implements a factory
 * for mapping a query to its builder.
 */
interface QueryBuilderFactory
{
    /**
     * Returns a query builder based on a query.
     *
     * @param StructuredQuery $query Query to be inspected
     * @return QueryBuilder Resulting query builder.
     */
    public function getBuilder(StructuredQuery $query): QueryBuilder;
}