<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-03
 * Time: 23:47
 * License: MIT
 */

use ArekX\PQL\Drivers\ArrayData\Driver;
use ArekX\PQL\Drivers\MySQL\MySqlDriver;
use ArekX\PQL\Drivers\MySQL\QueryBuilder;
use ArekX\PQL\Select;

require __DIR__ . '/vendor/autoload.php';

$driver = new MySqlDriver();
$builder = new QueryBuilder();

$query = Select::fromTable(["'custom name'" => 'sakila', 'b' => 'killua'])
    ->columns('a.*', 'b.*', 'a.id', 'b');

echo $builder->build($query)->getQuery() . PHP_EOL;




