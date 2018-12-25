<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\DataSources;

use ArekX\PQL\Instance;
use ArekX\PQL\Query;
use ArekX\PQL\Values\ValueInterface;

class IterableSource implements DataSourceInterface, ValueInterface
{
    protected $dataSource = [];

    /** @var Query */
    protected $query;

    public static function from($dataSource): DataSourceInterface
    {
        return Instance::ensure(static::class, [$dataSource]);
    }

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function select($names) : Query
    {
        if (!is_array($names)) {
            $names = [$names];
        }

        return $this->query = Query::from($names, $this);
    }

    public function query(?Query $query = null): Query
    {
        if ($query === null) {
            return $this->query;
        }

        $query->dataSource = $this;

        return $this->query = $query;
    }

    public function getResults()
    {
        // TODO: Implement results() method.
    }

    /**
     * Extracts value from interface.
     *
     * @return mixed
     */
    public function extractValue()
    {
        // TODO: Implement extractValue() method.
    }
}