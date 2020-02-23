<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL;

use ArekX\PQL\Exceptions\InvalidInstanceException;

class Factory
{
    protected static $map = [];

    public static function from($class, $params = [])
    {
        $resolvedClass = static::resolve($class);

        if (is_callable($resolvedClass)) {
            $instance = $resolvedClass(...$params);
        } else {
            $instance = new $resolvedClass(...$params);
        }

        if (!($instance instanceof $class)) {
            throw new InvalidInstanceException($instance, $class);
        }

        return $instance;
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