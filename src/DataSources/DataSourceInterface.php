<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\DataSources;

use ArekX\PQL\Query;

interface DataSourceInterface
{
    public static function from($dataSource): DataSourceInterface;
    public function select($names) : Query;
    public function query(Query $query);
    public function getResults();
}