<?php

namespace ArekX\PQL\Data;

use ArekX\PQL\Contracts\RawQuery;

class RawSqlQuery implements RawQuery
{
    protected string $sql;
    protected array $params;
    protected array $config;

    public static function create($sql, array $params = []): self
    {
        return new static($sql, $params);
    }

    public function __construct(string $sql, array $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    public function getQuery(): string
    {
       return $this->sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}