<?php

namespace ArekX\PQL\Contracts;

/**
 * Interface or raw processed queries.
 */
interface RawQuery
{
    /**
     * Return the raw query
     *
     * Query format depends on the driver in use.
     *
     * @return mixed
     */
    public function getQuery();

    /**
     * Return params to be bound to the query.
     *
     * Array returned should have the key as param name
     * and value as the value to be bound.
     *
     * Value depends on the driver in use.
     *
     * If null is returned, no params are in use.
     *
     * @return array|null
     */
    public function getParams(): ?array;

    /**
     * Return additional config to be set for this query.
     *
     * Configuration should be an associative array with key meaning the
     * driver dependent configuration name and value being the driver dependent value.
     *
     * If null is returned, no configuration is set.
     *
     * @return array|null
     */
    public function getConfig(): ?array;
}