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
    const AS_SINGLE = 'single';
    const AS_ALL = 'all';
    const AS_COLUMN = 'column';
    const AS_SCALAR = 'scalar';
    const AS_MAP = 'map';

    public function run(Query $query, string $as, array $asConfig = null);
}