<?php

namespace ArekX\PQL\Contracts;

interface FilteredQuery
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


    public function limit($rows): self;

    public function offset($rows): self;
}