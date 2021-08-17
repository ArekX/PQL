<?php

namespace ArekX\PQL\Drivers\MySQL\Builders;

use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\RawSqlQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildAsTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildConditionTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildFromTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildJoinTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLimitTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildLiteralTrait;
use ArekX\PQL\Drivers\MySQL\Traits\BuildParentQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildStructuredQuery;
use ArekX\PQL\Drivers\MySQL\Traits\BuildWhereTrait;
use ArekX\PQL\Drivers\MySQL\Traits\QuoteNameTrait;
use ArekX\PQL\Raw;

class DeleteBuilder implements QueryBuilderChild
{
    use BuildParentQuery;
    use BuildJoinTrait;
    use BuildWhereTrait;
    use BuildLimitTrait;
    use BuildFromTrait;

    public function build(StructuredQuery $query, QueryBuilderState $state): RawQuery
    {
        $structure = $query->getStructure();

        $parts = array_filter([
            'DELETE',
            $this->buildFrom($structure, $state),
            $this->buildJoins($structure, $state),
            $this->buildWhere($structure, $state),
            $this->buildLimit($structure)
        ]);

        return RawSqlQuery::create(
            implode($state->get('queryGlue'), $parts),
            $state->getQueryParams()->getParams()
        );
    }
}