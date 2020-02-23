<?php

namespace tests\Factory;

use ArekX\PQL\Factory;

/**
 * Created by Aleksandar Panic
 * Date: 26-Dec-18
 * Time: 22:32
 * License: MIT
 */

class TestDerivedClassWithParams extends TestClassWithParams
{
    public $param3;

    public function __construct($param1, $param2, $param3)
    {
        $this->param3 = $param3;
        parent::__construct($param1, $param2);
    }
}