<?php

namespace ArekX\PQL\Contracts;

/**
 * Represents a structured query used by the database
 * driver to perform operations on it.
 */
interface StructuredQuery
{
    /**
     * Create a new instance of this query
     *
     * @return static
     */
    public static function create(): self;

    /**
     * Return an array data representing the
     * structure of this query.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Return part value or null if part is not defined.
     *
     * @param string $part
     * @return mixed
     */
    public function get(string $part);

    /**
     * Set a part to be used in the query.
     *
     * Part name and value depend on the driver and builder used.
     *
     * @param string $part Part of the query to define.
     * @param mixed $value Value of the part
     * @return $this
     */
    public function use(string $part, $value): self;

    /**
     * Append part to the query or set if not used.
     *
     * If the part was not previously defined then the
     * action of this function is equivalent to use function.
     *
     * @see StructuredQuery::use()
     * @param string $part Part to use
     * @param mixed $value Value of the part.
     * @return $this
     */
    public function append(string $part, $value): self;
}