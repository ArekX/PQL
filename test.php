<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-03
 * Time: 23:47
 * License: MIT
 */

use ArekX\PQL\Drivers\MySQL\MySqlDriver;
use ArekX\PQL\Drivers\MySQL\QueryBuilder;
use ArekX\PQL\Helpers\Op;
use ArekX\PQL\Raw;
use ArekX\PQL\Select;

require __DIR__ . '/vendor/autoload.php';

$driver = new MySqlDriver();
$builder = new QueryBuilder();

$query = Select::table(["'custom name'" => 'sakila', 'b' => 'killua', 'c' => 'sss'])
    ->columns('a.*', 'b.*', 'a.id', 'b', Raw::query("OPA CUPA"))
    ->innerJoin(['ds' => Raw::query('(t1 CROSS JOIN t2 CROSS JOIN t3)')], 'on')
    ->leftJoin(['b' => 'hk'], ['multi', [
        'a.id' => Raw::query('(t1 CROSS JOIN t2 CROSS JOIN t3)')]
    ])
    ->where(Op::and(
        Op::not(Op::search(['opa.cupa' => null, 'a' => [1, 2, 3]])),
        Op::or(Op::compare('is_active', 1), Raw::query('OPA CUPA')),
        Op::likeSearch('st', '333'),
        Select::table('user')->columns('1')->where([
            'and',
            Op::compare('is_deleted', 0),
            Op::in('user_ids', Op::val([1, 2, 3, 4, 5]))
        ])
    ))
    ->offset(15)->limit(5);

$result = $builder->build($query);
echo $result->getQuery() . PHP_EOL;
print_r($result->getParams());




