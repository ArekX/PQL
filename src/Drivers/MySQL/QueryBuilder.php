<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\RawQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Drivers\MySQL\Builders\SelectBuilder;
use ArekX\PQL\Exceptions\MissingBuilderException;
use ArekX\PQL\Select;

class QueryBuilder implements \ArekX\PQL\Contracts\QueryBuilder
{
    const BUILDERS = [
        Select::class => SelectBuilder::class
    ];

    protected array $builders = [];

    public function build(StructuredQuery $query): RawQuery
    {
        return $this->getBuilder($query)->build($query);
    }

    public function getBuilder(StructuredQuery $query): \ArekX\PQL\Contracts\QueryBuilder
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

        return $this->builders[$queryClass];
    }
}