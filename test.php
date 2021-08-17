<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-03
 * Time: 23:47
 * License: MIT
 */

use ArekX\PQL\Drivers\MySQL\MySqlDriver;
use ArekX\PQL\Drivers\MySQL\MySqlQueryBuilder;
use ArekX\PQL\Helpers\Op;
use ArekX\PQL\Raw;
use ArekX\PQL\Select;

require __DIR__ . '/vendor/autoload.php';

$driver = new MySqlDriver();
$builder = new MySqlQueryBuilder();

$query = Select::table(["'custom name'" => 'sakila', 'b' => ' x', 'c' => 'sss'])
    ->columns('a.*', 'b.*', 'a.id', 'b', Raw::query('fzs'))
    ->where([
        'and',
        Op::eq('is_active', 1),
        Op::gte('create_time', 212414),
        Op::between('time', 12, 44)
    ])
    ->offset(15)->limit(5);


$q = \ArekX\PQL\Update::item('idemo', [
    'a' => 1,
    'b' => true,
    'c' => null,
    'das.sss' => 'sasfasfasfsafsafasf'
]);
echo $builder->build($q)->getQuery();
var_dump($builder->build($q)->getParams());
$result = $builder->build($query);
echo $result->getQuery();
var_dump($result->getParams());
//echo \ArekX\PQL\Drivers\MySQL\DebugQuery::getString($result) . PHP_EOL;




