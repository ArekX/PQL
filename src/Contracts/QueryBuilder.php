<?php

namespace ArekX\PQL\Contracts;

/**
 * Interface which implements building structured
 * queries into Raw queries usable by the driver.
 */
interface QueryBuilder
{
    /**
     * Build the structured query and return the built query as a raw query.
     *
     *
     * @param StructuredQuery $query Query to be built.
     * @param QueryBuilderState|null $state State to be used for building the query.
     * @return RawQuery Resulting query
     */
    public function build(StructuredQuery $query, QueryBuilderState $state = null): RawQuery;
}