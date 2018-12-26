<?php
/**
 * Created by Aleksandar Panic
 * Date: 25-Dec-18
 * Time: 22:28
 * License: MIT
 */

namespace ArekX\PQL;

class Factory
{
    protected static $map = [];

    public static function from($class, $params = [])
    {
        $class = static::resolve($class);

        if (is_callable($class)) {
            return $class(...$params);
        }

        return new $class(...$params);
    }

    public static function override($class, $toClass)
    {
        static::$map[$class] = $toClass;
    }

    public static function map(array $mapList)
    {
        foreach ($mapList as $fromClass => $toDefinition) {
            static::override($fromClass, $toDefinition);
        }
    }

    protected static function resolve($class)
    {
        if (!is_string($class) || empty(static::$map[$class])) {
            return $class;
        }

        return static::resolve(static::$map[$class]);
    }
}