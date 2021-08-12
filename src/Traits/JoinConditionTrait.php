<?php

namespace ArekX\PQL\Traits;

trait JoinConditionTrait
{
    protected ?array $join = null;

    public function setJoin($joinExpression): self
    {
        $this->join = $joinExpression;
        return $this;
    }

    public function join(string $type, $tableExpression, $onExpression): self
    {
        if ($this->join === null) {
            $this->join = [];
        }

        $this->join[] = [$type, $tableExpression, $onExpression];
        return $this;
    }

    public function leftJoin($tableExpression, $onExpression): self
    {
        return $this->join('left', $tableExpression, $onExpression);
    }

    public function rightJoin($tableExpression, $onExpression): self
    {
        return $this->join('right', $tableExpression, $onExpression);
    }

    public function innerJoin($tableExpression, $onExpression): self
    {
        return $this->join('inner', $tableExpression, $onExpression);
    }

    public function fullOuterJoin($tableExpression, $onExpression): self
    {
        return $this->join('full outer', $tableExpression, $onExpression);
    }
}