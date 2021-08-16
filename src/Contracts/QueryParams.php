<?php

namespace ArekX\PQL\Contracts;

interface QueryParams
{
    public function addParam($value): string;

    public function mergeParams(array $params);

    public function getParams(): array;
}