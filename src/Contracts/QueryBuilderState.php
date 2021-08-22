<?php

namespace ArekX\PQL\Contracts;

/**
 * Interface which implements the state
 * for query builder.
 */
interface QueryBuilderState
{
    /**
     * Return the value stored in the state by name.
     *
     * @param string $name Name of the state item to be returned.
     * @param mixed $default Default value to be returned of the item is not found.
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * Store the value in the state.
     *
     * @param string $name Name of the value
     * @param mixed $value Value to be stored
     */
    public function set(string $name, $value): void;
}