<?php
/**
 * Created by Aleksandar Panic
 * Date: 25-Dec-18
 * Time: 22:28
 * License: MIT
 */

namespace ArekX\PQL;

use ArekX\PQL\Exceptions\InvalidInstanceException;

class Instance
{
    protected static $map = [];

    public static function from($class, $params)
    {
        $class = static::resolve($class);

        if (is_callable($class)) {
            return $class(...$params);
        }

        return new $class(...$params);
    }

    public static function ensure($class, $params)
    {
        $instance = static::from($class, $params);

        if (!($instance instanceof $class)) {
            throw new InvalidInstanceException($instance, $class);
        }

        return $instance;
    }

    public static function override($class, $toClass)
    {
        static::$map[$class] = $toClass;
    }

    public static function remove($class)
    {
        if (!empty(static::$map[$class])) {
            unset(static::$map[$class]);
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