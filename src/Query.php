<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL;

/**
 * Class Query
 * @package ArekX\PQL
 *
 * Query object
 */
class Query
{
    use FactoryTrait;

    protected $select;
    protected $from;
    protected $where;
    protected $join;
    protected $order;
    protected $group;

    public function select($names)
    {
        $this->select = $names;
        return $this;
    }

    public function where($filter)
    {
        $this->where = $filter;
        return $this;
    }

    public function from($source)
    {
        $this->from = $source;
        return $this;
    }

    public function join($sources)
    {
        $this->join = $sources;
        return $this;
    }

    public function order($by)
    {
        $this->order = $by;
        return $this;
    }

    public function group($by)
    {
        $this->group = $by;
        return $this;
    }

    public function readRaw($raw)
    {
        $this->select = $raw['select'];
        $this->from = $raw['from'];
        $this->where = $raw['where'];
        $this->join = $raw['join'];
        $this->order = $raw['order'];
        $this->group = $raw['group'];

        return $this;
    }

    public function raw()
    {
        return [
            'select' => $this->select,
            'from' => $this->from,
            'where' => $this->where,
            'join' => $this->join,
            'order' => $this->order,
            'group' => $this->group,
        ];
    }

    public function copy()
    {
        return clone $this;
    }
}