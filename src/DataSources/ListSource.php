<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\DataSources;

use ArekX\PQL\Query;

class ListSource implements DataSourceInterface
{
    protected $dataSource = [];

    /** @var Query */
    protected $query;

    public static function from($dataSource): DataSourceInterface
    {
        return new static($dataSource);
    }

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function select($names) : Query
    {
        if ($this->query) {
            return $this->query;
        }

        return $this->query = Query::from($names, $this);
    }

    public function query(Query $query)
    {
        $this->query = $query;
    }

    public function getResults()
    {
        // TODO: Implement results() method.
    }
}