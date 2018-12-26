<?php

namespace tests\factory;

use ArekX\PQL\Factory;

/**
 * Created by Aleksandar Panic
 * Date: 26-Dec-18
 * Time: 22:32
 * License: MIT
 */

class TestDerivedClass extends TestClass
{
    public static function makeFrom()
    {
        return Factory::from(self::class);
    }
}