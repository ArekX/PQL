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

class AssociativeOperator implements OperatorInterface
{
    use FactoryTrait;

    /** @var Filter */
    protected $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function evaluate(array $rule): ?bool
    {
        if (!empty($rule[0])) {
            return null;
        }

        foreach ($rule as $key => $expected) {
            if ($expected != $this->filter->from->get($key)) {
                return false;
            }
        }

        return true;
    }
}