<?php

namespace ArekX\PQL\Contracts;

interface QueryBuilderChild
{
    public function setParent(QueryBuilder $parent);

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery;
}