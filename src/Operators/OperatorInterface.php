<?php

namespace ArekX\PQL\Operators;

use ArekX\PQL\Filter;

/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

interface OperatorInterface
{
    /**
     * Create new instance of operator.
     *
     * @param Filter $filter Filter instance which is the parent of this operator
     * @param null|string|array| $definition Starting definition (left side of the operator)
     * @param array $params Input params which will be resolved.
     * @return OperatorInterface
     */
    public static function create(Filter $filter, $definition = null): OperatorInterface;

    /**
     * Return filter parent for further chaining.
     *
     * @return Filter
     */
    public function glue(): Filter;

    public function extractDefinition(): array;

    public static function fromDefinition(Filter $filter, array $definition): OperatorInterface;
}