<?php
/**
 * Created by Aleksandar Panic
 * Date: 27-Dec-18
 * Time: 00:14
 * License: MIT
 */

namespace ArekX\PQL;


trait FactoryTrait
{
    public static function create(...$params)
    {
        return Factory::from(static::class, $params);
    }
}