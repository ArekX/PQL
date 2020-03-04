<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-02-23
 * Time: 13:19
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData\Filter;


use ArekX\PQL\Drivers\ArrayData\Contracts\OperatorInterface;
use ArekX\PQL\Drivers\ArrayData\Driver;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\AndOrOperator;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\AssociativeOperator;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\CompareOperator;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\InOperator;
use ArekX\PQL\Drivers\ArrayData\Filter\Operators\SearchOperator;
use ArekX\PQL\Drivers\ArrayData\From;
use ArekX\PQL\FactoryTrait;

class Filter
{
    use FactoryTrait;

    protected $operators;

    /** @var From */
    public $from;

    /** @var Driver */
    public $driver;

    public function __construct(Driver $driver)
    {
        $this->operators = $this->createOperators();
        $this->driver = $driver;
        $this->from = $driver->from;
    }

    /**
     *
     * @return OperatorInterface[]
     */
    public function createOperators()
    {
        return [
            AssociativeOperator::create($this),
            AndOrOperator::create($this),
            CompareOperator::create($this),
            SearchOperator::create($this),
            InOperator::create($this),
        ];
    }

    public function evaluate(array $filter): bool
    {
        if (empty($filter)) {
            return true;
        }

        foreach ($this->operators as $operator) {
            $result = $operator->evaluate($filter);

            if ($result !== null) {
                return $result;
            }
        }

        throw new \Exception('Invalid filter encountered: ' . json_encode($filter));
    }
}