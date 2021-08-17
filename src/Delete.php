<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\FilteredQuery;
use ArekX\PQL\Contracts\JoinQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Traits\JoinConditionTrait;
use ArekX\PQL\Traits\FilterConditionTrait;

class Delete implements StructuredQuery, FilteredQuery, JoinQuery
{
    use FilterConditionTrait;
    use JoinConditionTrait;

    protected $table = null;

    public static function create(): self
    {
        return new Delete();
    }

    public static function fromTable($tableExpression): self
    {
        return static::create()->from($tableExpression);
    }

    public function from($tableExpression): self
    {
        $this->table = $tableExpression;
        return $this;
    }

    public function getStructure(): array
    {
        return [
            'from' => $this->table,
            'where' => $this->where,
            'join' => $this->join,
            'limit' => $this->limit,
            'offset' => $this->offset
        ];
    }
}