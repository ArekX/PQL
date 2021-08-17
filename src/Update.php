<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\FilteredQuery;
use ArekX\PQL\Contracts\JoinQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Traits\JoinConditionTrait;
use ArekX\PQL\Traits\FilterConditionTrait;

class Update implements StructuredQuery, FilteredQuery, JoinQuery
{
    use FilterConditionTrait;
    use JoinConditionTrait;

    protected $table = null;
    protected $values = null;

    public static function create(): self
    {
        return new Update();
    }

    public static function item($tableExpression, $setExpression): self
    {
        return static::create()->table($tableExpression)->set($setExpression);
    }

    public function table($tableExpression): self
    {
        $this->table = $tableExpression;
        return $this;
    }

    public function set($values): self
    {
        $this->values = $values;
        return $this;
    }

    public function getStructure(): array
    {
        return [
            'table' => $this->table,
            'set' => $this->values,
            'where' => $this->where,
            'join' => $this->join,
            'limit' => $this->limit,
            'offset' => $this->offset
        ];
    }
}