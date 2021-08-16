<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\QueryBuilderChild;
use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Data\PrefixQueryParams;
use ArekX\PQL\Drivers\MySQL\Builders\RawBuilder;
use ArekX\PQL\Drivers\MySQL\Builders\SelectBuilder;
use ArekX\PQL\Drivers\MySQL\Builders\UnionBuilder;
use ArekX\PQL\Exceptions\MissingBuilderException;
use ArekX\PQL\Raw;
use ArekX\PQL\Select;
use ArekX\PQL\Union;

class QueryBuilder implements \ArekX\PQL\Contracts\QueryBuilder
{
    const BUILDERS = [
        Select::class => SelectBuilder::class,
        Raw::class => RawBuilder::class,
        Union::class => UnionBuilder::class
    ];

    protected array $builders = [];

    public function build(StructuredQuery $query, QueryBuilderState $state = null): RawQuery
    {
        $state = $state ?? MySqlBuilderState::create();
        return $this->getBuilder($query)->build($query, $state);
    }

    public function getBuilder(StructuredQuery $query): QueryBuilderChild
    {
        $queryClass = get_class($query);

        if (!empty($this->builders[$queryClass])) {
            return $this->builders[$queryClass];
        }

        if (empty(self::BUILDERS[$queryClass])) {
            throw new MissingBuilderException($query);
        }

        $builderClass = self::BUILDERS[$queryClass];
        $this->builders[$queryClass] = new $builderClass();

        $this->builders[$queryClass]->setParent($this);

        return $this->builders[$queryClass];
    }
}