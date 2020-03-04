<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL;

use ArekX\PQL\Contracts\DriverInterface;

/**
 * Query object representing querying for data.
 *
 * Class Query
 * @package ArekX\PQL
 */
class Query
{
    const AS_SINGLE = 'single';
    const AS_ALL = 'all';
    const AS_COLUMN = 'column';
    const AS_SCALAR = 'scalar';
    const AS_COUNT = 'count';
    const AS_MAP = 'map';

    use FactoryTrait;

    /**
     * Selection statement for selecting names for data.
     * @var string|array
     */
    public $select;

    /**
     * From statement for defining a data source for data.
     * @var string|array|Query
     */
    public $from;

    /**
     * Filter statement for defining filtering of data source.
     * @var array
     */
    public $where;

    /**
     * Join statement for defining how to join multiple data sources.
     * @var array
     */
    public $join;

    /**
     * Order statement for defining how to sort results.
     * @var array
     */
    public $order;

    /**
     * Group statement for defining how to group data results.
     * @var array
     */
    public $group;

    /**
     * Return As type
     * @var string
     */
    public $returnAs = self::AS_ALL;

    /**
     * Return As config
     * @var array
     */
    public $returnAsConfig;

    /**
     * Driver used to process the query
     *
     * @var null|DriverInterface
     */
    public $driver;


    /**
     * Selects one or more names from data source to be used.
     *
     * @param string|array $names
     * @return $this
     */
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

    public function driver($driver)
    {
        $this->driver = $driver;
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

    public function as(string $as, array $asConfig = null)
    {
        $this->returnAs = $as;
        $this->returnAsConfig = $asConfig;
        return $this;
    }

    public function asSingle()
    {
        return $this->as(self::AS_SINGLE);
    }

    public function asAll()
    {
        return $this->as(self::AS_ALL);
    }

    public function asColumn($key = null)
    {
        return $this->as(self::AS_COLUMN, ['key' => $key]);
    }

    public function asCount()
    {
        return $this->as(self::AS_COUNT);
    }

    public function asMap($key, $value)
    {
        return $this->as(self::AS_MAP, ['key' => $key, 'value' => $value]);
    }

    public function asScalar($key = null)
    {
        return $this->as(self::AS_SCALAR, ['key' => $key]);
    }

    public function run(Query $parentQuery = null)
    {
        return $this->driver->run($this, $parentQuery);
    }

    public function copy()
    {
        return clone $this;
    }
}