<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-03
 * Time: 23:47
 * License: MIT
 */

use ArekX\PQL\Drivers\MySQL\MySqlDriver;
use ArekX\PQL\Drivers\MySQL\MySqlQueryBuilder;
use function ArekX\PQL\equals;
use function ArekX\PQL\notOp;
use function ArekX\PQL\raw;
use function ArekX\PQL\select;

require __DIR__ . '/vendor/autoload.php';

$driver = new MySqlDriver();
$builder = new MySqlQueryBuilder();

$driver->configure(
    'mysql:host=192.168.0.17;dbname=tester;charset=utf8mb4',
    'root',
    'root'
);

$q = select([
    'name',
    'total' => raw('COUNT(*)')
])->from('table')
    ->groupBy('name')
    ->orderBy(['total' => 'desc'])
    ->having(notOp(equals('total', 4)));

$s = $driver->fetchAll($builder->build($q));

var_dump($s);

//echo \ArekX\PQL\Drivers\MySQL\DebugQuery::getString($result) . PHP_EOL;




