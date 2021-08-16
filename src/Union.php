<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\FilteredQuery;
use ArekX\PQL\Contracts\JoinQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Traits\JoinConditionTrait;
use ArekX\PQL\Traits\FilterConditionTrait;

class Union implements StructuredQuery
{
    protected $initial = null;
    protected array $union = [];

    public function getStructure(): array
    {
        return [
            'initial' => $this->initial,
            'union' => $this->union
        ];
    }

    public static function create(StructuredQuery $initial): self
    {
        return (new Union())->setInitial($initial);
    }

    public function setInitial(StructuredQuery $query): self
    {
        $this->initial = $query;
        return $this;
    }

    public function unionAll(StructuredQuery $query): self
    {
        $this->union[] = ['union all', $query];
        return $this;
    }

    public function union(StructuredQuery $query): self
    {
        $this->union[] = ['union', $query];
        return $this;
    }
}