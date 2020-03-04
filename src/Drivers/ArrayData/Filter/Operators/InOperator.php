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

class InOperator implements OperatorInterface
{
    use FactoryTrait;

    protected $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function evaluate(array $rule): ?bool
    {
        if ($rule[0] !== 'in') {
            return null;
        }

        $subject = $this->filter->from->get($rule[1]);
        $search = $rule[2];

        return in_array($subject, $search);
    }
}