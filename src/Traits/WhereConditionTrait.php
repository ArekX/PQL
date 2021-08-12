<?php

namespace ArekX\PQL\Traits;

use ArekX\PQL\Helpers\Op;

trait WhereConditionTrait
{
    protected ?array $where = null;

    public function where($whereExpression): self
    {
        $this->where = $whereExpression;
        return $this;
    }

    public function andWhere($whereExpression): self
    {
        if ($this->where === null) {
            return $this->where($whereExpression);
        }

        $this->where = Op::and($this->where, $whereExpression);
        return $this;
    }

    public function orWhere($whereExpression): self
    {
        if ($this->where === null) {
            return $this->where($whereExpression);
        }

        $this->where = Op::or($this->where, $whereExpression);
        return $this;
    }
}