<?php

namespace ArekX\PQL\Contracts;

interface QueryBuilder
{
    public function build(StructuredQuery $query, QueryBuilderState $state = null): RawQuery;
}