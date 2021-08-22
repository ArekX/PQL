<?php

namespace ArekX\PQL\Contracts;

/**
 * Interface for building query parameters.
 */
interface ParamsBuilder
{
    /**
     * Add a value to the query params builder.
     *
     * Value can be any value supported by the params builder.
     *
     * @param mixed $value Value to be added.
     * @param mixed $type Driver specific type of the value to be added. If null, type will be inferred.
     * @return mixed Wrapped replacement for the value which is safe to be used in the query.
     */
    public function wrapValue($value, $type = null);

    /**
     * Add a specific parameter by key and value.
     *
     * @param mixed $key Key of the parameter to be added
     * @param mixed $value Value of the parameter to be added.
     * @param mixed $type Driver specific type of the value to be added. If null, type will be inferred.
     */
    public function add($key, $value, $type = null): void;

    /**
     * Returns an added parameter by key.
     * @param mixed $key Key of the parameter.
     * @return mixed Value of the parameter.
     */
    public function get($key);

    /**
     * Build and return the resulting parameter array.
     *
     * @return array
     */
    public function build(): array;
}