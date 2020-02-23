<?php

namespace tests\Factory;

use ArekX\PQL\FactoryTrait;

/**
 * Created by Aleksandar Panic
 * Date: 26-Dec-18
 * Time: 22:32
 * License: MIT
 */

class TestClassWithParams
{
    use FactoryTrait;

    public $param1;
    public $param2;

    public function __construct($param1, $param2)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
    }
}