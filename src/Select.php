<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\FilteredQuery;
use ArekX\PQL\Contracts\JoinQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Traits\JoinConditionTrait;
use ArekX\PQL\Traits\FilterConditionTrait;

class Select implements StructuredQuery, FilteredQuery, JoinQuery
{
    use FilterConditionTrait;
    use JoinConditionTrait;

    protected $from = null;
    protected $select = null;
    protected ?array $join = null;
    protected ?array $order = null;
    protected ?array $having = null;
    protected ?array $group = null;
    protected ?array $union = null;
    protected $limit = null;
    protected $offset = null;

    public function getStructure(): array
    {
        return [
            'from' => $this->from,
            'select' => $this->select,
            'join' => $this->join,
            'order' => $this->order,
            'having' => $this->having,
            'where' => $this->where,
            'group' => $this->group,
            'limit' => $this->limit,
            'offset' => $this->offset
        ];
    }

    public static function table($tableExpression): self
    {
        return static::create()->from($tableExpression);
    }

    public static function create(): self
    {
        return new Select();
    }

    public function from($tableExpression): self
    {
        $this->from = $tableExpression;
        return $this;
    }

    public function columns(...$columns): self
    {
        $this->select = count($columns) === 1 ? $columns[0] : $columns;
        return $this;
    }

    public function orderBy($orderExpression): self
    {
        $this->order = $orderExpression;
        return $this;
    }

    public function having($havingExpression): self
    {
        $this->having = $havingExpression;
        return $this;
    }

    public function groupBy($groupExpression): self
    {
        $this->group = $groupExpression;
        return $this;
    }
}