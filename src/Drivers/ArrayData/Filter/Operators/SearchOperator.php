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

class SearchOperator implements OperatorInterface
{
    use FactoryTrait;

    protected $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function evaluate(array $rule): ?bool
    {
        if ($rule[0] !== 'search' && $rule[0] !== 'startsWith' && $rule[0] !== 'endsWith') {
            return null;
        }

        $subject = $this->filter->from->get($rule[1]);
        $search = $rule[2];

        switch ($rule[0]) {
            case 'search':
                return stristr($subject, $search) !== false;
            case 'startsWith':
                return $search === "" || substr_compare($subject, $search, 0, strlen($search), true) === 0;
            case 'endsWith':
                return $search === "" || substr_compare($subject, $search, -strlen($search), true) === 0;
        }

        return null;
    }
}