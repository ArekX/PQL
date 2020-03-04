<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL\Contracts;


use ArekX\PQL\Query;

interface DriverInterface
{
    public function run(Query $query, Query $parentData = null);
}