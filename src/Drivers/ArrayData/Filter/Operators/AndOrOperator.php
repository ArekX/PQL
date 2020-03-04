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

class AndOrOperator implements OperatorInterface
{
    use FactoryTrait;

    protected $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function evaluate(array $rule): ?bool
    {
        $operator = $rule[0];

        if ($operator !== 'and' && $operator !== 'or') {
            return null;
        }

        $max = count($rule);
        for ($i = 1; $i < $max; $i++) {
            $result = $this->filter->evaluate($rule[$i]);

            if (!$result && $operator === 'and') {
                return false;
            }

            if ($result && $operator === 'or') {
                return true;
            }
        }

        return $operator === 'and';
    }
}