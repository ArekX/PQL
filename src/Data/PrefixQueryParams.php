<?php

namespace ArekX\PQL\Data;

use ArekX\PQL\Contracts\QueryParams;

class PrefixQueryParams implements QueryParams
{
    protected string $prefix;
    protected int $start;

    protected array $params = [];

    public function __construct(string $prefix = ':t', int $start = 0)
    {
        $this->prefix = $prefix;
        $this->start = $start;
    }

    public function addParam($value): string
    {
        $key = $this->prefix . $this->start++;
        $this->params[$key] = $value;
        return $key;
    }

    public function mergeParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }
    }

    public function getParams(): array
    {
        return $this->params;
    }
}