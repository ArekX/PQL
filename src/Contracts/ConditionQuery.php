<?php

namespace ArekX\PQL\Contracts;

interface ConditionQuery
{
    /**
     * @param $whereExpression
     * @return $this
     */
    public function where($whereExpression): self;

    /**
     * @param $whereExpression
     * @return $this
     */
    public function andWhere($whereExpression): self;

    /**
     * @param $whereExpression
     * @return $this
     */
    public function orWhere($whereExpression): self;
}