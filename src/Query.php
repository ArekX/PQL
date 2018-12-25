<?php
/**
 * Created by Aleksandar Panic
 * Date: ${DATE}
 * Time: ${TIME}
 * License: MIT
 */

namespace ArekX\PQL;


use ArekX\PQL\DataSources\DataSourceInterface;

class Query implements SelectableOwnerInterface
{
    /** @var Filter */
    public $filter;

    /** @var array */
    public $names;

    /** @var Order */
    public $order;

    /** @var Mapper */
    public $mapper;

    /** @var Reducer */
    public $reducer;

    /** @var Limiter */
    public $limiter;

    /** @var DataSourceInterface */
    public $dataSource;

    public function __construct(array $names, DataSourceInterface $dataSource = null)
    {
        $this->names = $names;
        $this->dataSource = $dataSource;
    }

    public static function from(array $names, DataSourceInterface $dataSource = null)
    {
        return Instance::ensure(static::class, [$names, $dataSource]);
    }

    public function filter(): Filter
    {
        if ($this->filter) {
            return $this->filter;
        }

        return $this->filter = Filter::from($this, []);
    }

    public function order(): Order
    {
        if ($this->order) {
            return $this->order;
        }

        return $this->order = Order::from($this);
    }

    public function map(): Mapper
    {
        if ($this->mapper) {
            return $this->mapper;
        }

        return $this->mapper = Mapper::from($this);
    }

    public function reduce(): Reducer
    {
        if ($this->reducer) {
            return $this->reducer;
        }

        return $this->reducer = Reducer::from($this);
    }

    public function fromSource(): DataSourceInterface
    {
        return $this->dataSource;
    }

    public function limit(): Limiter
    {
        if ($this->limiter) {
            return $this->limiter;
        }

        return $this->limiter = Limiter::as($this);
    }
}