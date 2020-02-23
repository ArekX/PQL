<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-02-23
 * Time: 13:19
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData\Filter;


use ArekX\PQL\Drivers\ArrayData\Contracts\OperatorInterface;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\AssociativeOperator;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\CompareOperator;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\SearchOperator;
use ArekX\PQL\FactoryTrait;

class Filter
{
    use FactoryTrait;

    protected $operators;

    public function __construct()
    {
        $this->operators = $this->createOperators();
    }

    /**
     *
     * @return OperatorInterface[]
     */
    public function createOperators()
    {
        return [
            AssociativeOperator::create($this),
            CompareOperator::create($this),
            SearchOperator::create($this)
        ];
    }

    public function parse(array $filter)
    {
        foreach ($this->operators as $operator) {
            if (!$operator->match($filter)) {
                return $operator->parse($filter);
            }
        }

        throw new \Exception('Could not parse filter:' . print_r($filter, true));
    }

    public function evaluate(array $filter, array $row): bool
    {

    }
}