<?php

namespace ArekX\PQL\Traits;

use ArekX\PQL\Helpers\Op;

trait FilterConditionTrait
{
    protected $where = null;
    protected $limit = null;
    protected $offset = null;

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


    public function limit($rows): self
    {
        $this->limit = $rows;
        return $this;
    }

    public function offset($rows): self
    {
        $this->offset = $rows;
        return $this;
    }
}