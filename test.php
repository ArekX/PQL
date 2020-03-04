<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-03
 * Time: 23:47
 * License: MIT
 */

use ArekX\PQL\Drivers\ArrayData\Driver;
use ArekX\PQL\Query;

require __DIR__ . '/vendor/autoload.php';

$driver = new Driver();

$data = [
    ['name' => 'jane', 'age' => 15],
    ['name' => 'doe', 'age' => 5],
    ['name' => 'doh', 'age' => 7],
    ['name' => 'john', 'age' => 11],
];

$results = Query::create()
    ->from($data)
    ->select([
        'name' => 'nameAs',
        'age' => 'ageAs'
    ])
    ->where(['startsWith', 'name', 'ja'])
    ->driver($driver)
    ->run();


print_r($results);



