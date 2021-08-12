<?php

namespace ArekX\PQL;

use ArekX\PQL\Contracts\ConditionQuery;
use ArekX\PQL\Contracts\JoinQuery;
use ArekX\PQL\Contracts\StructuredQuery;
use ArekX\PQL\Traits\JoinConditionTrait;
use ArekX\PQL\Traits\WhereConditionTrait;

class Select implements StructuredQuery, ConditionQuery, JoinQuery
{
    use WhereConditionTrait;
    use JoinConditionTrait;

    protected $from = null;
    protected $select = null;
    protected ?array $join = null;
    protected ?array $order = null;
    protected ?array $having = null;
    protected ?array $group = null;
    protected ?array $union = null;

    public function getStructure(): array
    {
        return [
            'from' => $this->from,
            'select' => $this->select,
            'join' => $this->join,
            'order' => $this->order,
            'having' => $this->having,
            'where' => $this->where,
            'group' => $this->group
        ];
    }

    public static function fromTable($tableExpression): self
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

    public function setUnion($unionExpression): self
    {
        $this->union = $unionExpression;
        return $this;
    }

    public function union(...$unionExpressions): self
    {
        if ($this->union === null) {
            $this->union = [];
        }

        $this->union[] = $unionExpressions;
        return $this;
    }
}