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

    protected $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function evaluate(array $rule): ?bool
    {
        if (!preg_match('/^(!?=|>=?|<=?|<>|range)$/', $rule[0])) {
            return null;
        }

        $getValue = $this->filter->from->get($rule[1]);
        $vsValue = $rule[2];

        switch ($rule[0]) {
            case '=':
                return $getValue == $vsValue;
            case '<>':
            case '!=':
                return $getValue != $vsValue;
            case '>=':
                return $getValue >= $vsValue;
            case '<=':
                return $getValue <= $vsValue;
            case '>':
                return $getValue > $vsValue;
            case '<':
                return $getValue < $vsValue;
        }

        if ($rule[0] == 'range') {
            $toValue = $this->filter->from->get($rule[3]);

            return $vsValue <= $getValue && $getValue <= $toValue;
        }

        return null;
    }
}