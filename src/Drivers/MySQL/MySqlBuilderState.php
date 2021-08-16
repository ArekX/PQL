<?php

namespace ArekX\PQL\Drivers\MySQL;

use ArekX\PQL\Contracts\QueryBuilderState;
use ArekX\PQL\Contracts\QueryParams;
use ArekX\PQL\Data\PrefixQueryParams;

class MySqlBuilderState implements QueryBuilderState
{
    protected $state = [
        'queryGlue' => PHP_EOL
    ];

    protected $queryParams;

    public static function create(): self
    {
        return new MySqlBuilderState();
    }

    public function __construct()
    {
        $this->queryParams = new PrefixQueryParams();
    }

    public function get($name)
    {
        return $this->state[$name];
    }

    public function set($name, $value)
    {
        return $this->state[$name] = $value;
    }

    public function getQueryParams(): QueryParams
    {
        return $this->queryParams;
    }

    public function setQueryParams(QueryParams $params)
    {
        $this->queryParams = $params;
    }
}