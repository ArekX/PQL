<?php

namespace ArekX\PQL\Contracts;

interface JoinQuery
{
    public function setJoin($joinExpression): self;

    public function join(string $type, $tableExpression, $onExpression): self;

    public function leftJoin($tableExpression, $onExpression): self;

    public function rightJoin($tableExpression, $onExpression): self;

    public function innerJoin($tableExpression, $onExpression): self;

    public function fullOuterJoin($tableExpression, $onExpression): self;
}