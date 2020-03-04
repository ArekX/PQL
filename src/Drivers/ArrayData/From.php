<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-04
 * Time: 22:18
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData;


use ArekX\PQL\FactoryTrait;
use ArekX\PQL\Query;

class From
{
    use FactoryTrait;

    /** @var Query */
    protected $query;

    /** @var Query|null */
    protected $parentQuery;

    /** @var int */
    protected $cursor = 0;

    public function useQuery(Query $query, Query $parentQuery = null)
    {
        $this->query = $query;
        $this->parentQuery = $parentQuery;
        $this->cursor = 0;
    }

    public function hasNext(): bool
    {
        return $this->cursor < count($this->query->from);
    }

    public function getFullRow(): array
    {
        return $this->query->from[$this->cursor];
    }

    public function get($key)
    {
        return Value::get($this->getFullRow(), $key);
    }

    public function next()
    {
        $this->cursor++;
    }
}