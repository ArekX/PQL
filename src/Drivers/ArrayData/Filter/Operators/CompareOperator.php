<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-02-23
 * Time: 13:23
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData\Filter\Operators;

use ArekX\PQL\Drivers\ArrayData\Contracts\OperatorInterface;
use ArekX\PQL\Drivers\ArrayData\Filter\Filter;
use ArekX\PQL\FactoryTrait;

class CompareOperator implements OperatorInterface
{
    use FactoryTrait;

    public function match($rule)
    {
        // TODO: Implement match() method.
    }

    public function evaluate($rule)
    {
        // TODO: Implement evaluate() method.
    }

    public function __construct(Filter $filter)
    {
    }

    public function parse($rule)
    {
        // TODO: Implement parse() method.
    }
}