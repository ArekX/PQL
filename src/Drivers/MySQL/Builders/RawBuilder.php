<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\PrefixQueryParams;
use ArekX\PQL\Data\RawSqlQuery;
use \ArekX\PQL\Drivers\MySQL\MySqlQueryBuilder as ParentBuilder;

class RawBuilder implements QueryBuilderChild
{
    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $state->getQueryParams()->mergeParams($structure['params']);

        return RawSqlQuery::create($structure['query'], $state->getQueryParams()->getParams());
    }

    public function setParent(QueryBuilder $parent)
    {
    }
}