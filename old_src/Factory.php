<?php
/**
 * Created by Aleksandar Panic
 * Date: 25-Dec-18
 * Time: 22:28
 * License: MIT
 */

namespace ArekX\PQL;

use ArekX\PQL\Exceptions\InvalidInstanceException;

class Factory
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

    public static function matchClass($class, $params, $matchClass = null)
    {
        $instance = static::from($class, $params);

        $matchClass = $matchClass ?: $class;

        if (!($instance instanceof $matchClass)) {
            throw new InvalidInstanceException($instance, $matchClass);
        }

        return $instance;
    }

    public static function matchInterfaces($class, $params, $interfaces = null)
    {
        $instance = static::from($class, $params);

        $interfaces = $interfaces ?: static::getInterfaceNames($class);

        foreach ($interfaces as $interface) {
            if (!($instance instanceof $interface)) {
                throw new InvalidInstanceException($instance, $interface);
            }
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

    protected static function getInterfaceNames($class)
    {
        static $cache = [];

        if (empty($cache[$class])) {
            $cache[$class] = (new \ReflectionClass($class))->getInterfaceNames();
        }

        return $cache[$class];
    }

    protected static function resolve($class)
    {
        if (!is_string($class) || empty(static::$map[$class])) {
            return $class;
        }

        return static::resolve(static::$map[$class]);
    }
}