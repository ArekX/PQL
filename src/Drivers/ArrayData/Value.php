<?php
/**
 * @author Aleksandar Panic
 **/

namespace ArekX\PQL\Drivers\ArrayData;

/**
 * Class Value Helper for handling values.
 */
class Value
{
    /**
     * Gets one value by a name from an array or an object.
     *
     * Value name can be in a dot notation.
     *
     * Example:
     * ```php
     * [
     *    'param' => [
     *       'name' => [
     *          'name' => 'value'
     *       ]
     *    ]
     * ]
     * ```
     *
     * Can be accessed as: `param.name.name`
     *
     *
     * @param mixed $object Object to get a value from.
     * @param string $name Name of the value to be got.
     * @param null|mixed $default Default value to be returned if name is not set in an object.
     * @return mixed|null Value or default value.
     */
    public static function get($object, $name, $default = null)
    {
        if (!is_array($object) && !is_object($object)) {
            return $default;
        }

        $hasProperty =
            (is_array($object) && array_key_exists($name, $object)) ||
            (is_object($object) && property_exists($object, $name));

        if (strpos($name, '.') === -1 || $hasProperty) {
            return static::resolveValue($object, $name, $default);
        }

        $parts = explode('.', $name);
        $walker = $object;

        $lastPart = array_pop($parts);

        foreach ($parts as $part) {
            if (is_array($walker) && array_key_exists($part, $walker)) {
                $walker = $walker[$part];
            } elseif (is_object($walker) && property_exists($walker, $part)) {
                $walker = $walker->{$part};
            } else {
                return $default;
            }
        }

        return static::resolveValue($walker, $lastPart, $default);
    }

    /**
     * Resolves a value from a object.
     *
     * Object can be array or an instance of a class.
     *
     * @param array|object $object Object from which value will be resolved.
     * @param string $name Name of the value to be resolved.
     * @param null|mixed $default Default value to be returned.
     * @return mixed|null
     */
    protected static function resolveValue($object, string $name, $default = null)
    {
        if (is_array($object) && array_key_exists($name, $object)) {
            return $object[$name];
        }

        if (is_object($object) && property_exists($object, $name)) {
            return $object->{$name};
        }

        return $default;
    }
}