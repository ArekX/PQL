<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-04
 * Time: 17:47
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData;


use ArekX\PQL\FactoryTrait;
use ArekX\PQL\Query;

class Selector
{
    use FactoryTrait;

    /** @var Driver */
    protected $driver;

    /** @var From */
    protected $from;

    /** @var Query */
    protected $query;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->from = $driver->from;
    }

    public function useQuery(Query $query)
    {
        $this->query = $query;
    }

    public function select()
    {
        if ($this->query->select === null) {
            return $this->from->getFullRow();
        }

        if (is_string($this->query->select)) {
            return $this->from->get($this->query->select);
        }

        $result = [];

        foreach ($this->query->select as $column => $as) {
            if (is_int($column)) {
                $column = $as;
            }

            if (is_callable($column)) {
                $result[$as] = $column($this->from);
                continue;
            }

            $result[$as] = $this->from->get($column);
        }

        return $result;
    }
}