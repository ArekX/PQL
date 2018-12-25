<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\Operators;

use ArekX\PQL\Exceptions\InvalidForDefinition;
use ArekX\PQL\Filter;
use ArekX\PQL\Instance;

abstract class BaseOperator implements OperatorInterface
{
    /** @var Filter */
    protected $filter;

    /**
     * BaseOperator constructor.
     * @param Filter $filter
     * @param null $definition
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param Filter $filter
     * @param null $definition
     * @return OperatorInterface
     */
    public static function create(Filter $filter, $definition = null): OperatorInterface
    {
        return Instance::ensure(static::class, [$filter]);
    }

    /**
     * @param Filter $filter
     * @param array $definition
     * @return OperatorInterface
     */
    public static function fromDefinition(Filter $filter, array $definition): OperatorInterface
    {
        return Instance::ensure(static::class, [$filter]);
    }

    /**
     * @return Filter
     */
    public function glue(): Filter
    {
        return $this->filter;
    }

    /**
     * @return array
     */
    public function extractDefinition(): array
    {
        return [
            'class' => static::class
        ];
    }
}