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
use ArekX\PQL\Values\ValueInterface;

abstract class BaseBinaryOperator extends BaseOperator implements BinaryOperatorInterface
{
    /** @var array|ValueInterface[] */
    protected $operandB = [];


    /** @var string|ValueInterface */
    protected $operandA;

    /** @var bool */
    protected $inverted = false;

    protected $operator = null;


    /**
     * BaseOperator constructor.
     * @param Filter $filter
     * @param null $definition
     */
    public function __construct(Filter $filter, $definition = null)
    {
        parent::__construct($filter);

        if ($definition) {
            $this->for($definition);
        }
    }

    /**
     * @param Filter $filter
     * @param null $definition
     * @return OperatorInterface
     */
    public static function create(Filter $filter, $definition = null): OperatorInterface
    {
        return Instance::ensure(static::class, [$filter, $definition]);
    }

    /**
     * @param Filter $filter
     * @param array $definition
     * @return OperatorInterface
     */
    public static function fromDefinition(Filter $filter, array $definition): OperatorInterface
    {
        /** @var BaseBinaryOperator $op */
        $op = Instance::ensure(static::class, [$filter, $definition]);

        [
            'operandA' => $op->operandA,
            'inverted' => $op->inverted,
            'operator' => $op->operator,
            'operandB' => $op->operandB
        ] = $definition;

        return $op;
    }

    /**
     * @return array
     */
    public function extractDefinition(): array
    {
        return parent::extractDefinition() + [
                'operandA' => $this->operandA,
                'inverted' => $this->inverted,
                'operator' => $this->operator,
                'operandB' => $this->operandB
            ];
    }

    public function not(): BinaryOperatorInterface
    {
        $this->inverted = !$this->inverted;

        /** @var BinaryOperatorInterface $this */
        return $this;
    }

    public function is($value): Filter
    {
        if (!is_null($value)) {
            return $this->eq($value);
        }

        return $this->op(BinaryOperatorInterface::OPERATOR_IS, $value);
    }

    public function in($value): Filter
    {
        if (!is_array($value)) {
            return $this->eq($value);
        }

        return $this->op(BinaryOperatorInterface::OPERATOR_IN, is_array($value) ? $value : [$value]);
    }

    public function between($fromValue, $toValue): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_BETWEEN, [$fromValue, $toValue]);
    }

    public function eq($value): Filter
    {
        if (is_array($value)) {
            return $this->in($value);
        }

        return $this->op(BinaryOperatorInterface::OPERATOR_EQ, $value);
    }

    public function gt($value): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_GT, $value);
    }

    public function lt($value): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_LT, $value);
    }

    public function gte($value): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_GTE, $value);
    }

    public function lte($value): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_LTE, $value);
    }

    public function exists($value): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_EXISTS, $value);
    }

    public function find($value, $type = self::SEARCH_MIDDLE): Filter
    {
        return $this->op(BinaryOperatorInterface::OPERATOR_LIKE, [$value, $type]);
    }

    protected function op($operator, $values)
    {
        $this->operator = $operator;
        $this->operandB = $values;

        return $this->glue();
    }


    /**
     * @param $definition
     * @return BinaryOperatorInterface
     */
    public function for($definition): BinaryOperatorInterface
    {
        if (is_array($definition)) {
            if (count($definition) === 2) {
                [$name, $value] = $definition;
                $opFn = is_array($value) ? 'in' : 'eq';

                if (is_null($value)) {
                    $opFn = 'is';
                }

                $value = [$value];
            } else if (count($definition) === 3) {
                [$name, $opFn, $value] = $definition;
            } else if (count($definition) === 4) {
                [$name, $inverted, $opFn, $value] = $definition;
                if ($inverted) {
                    $this->not();
                }
            } else {
                throw new InvalidForDefinition($definition);
            }

            $this->operandA = $name;

            if (!is_array($value)) {
                $value = [$value];
            }

            ([$this, $opFn])(...$value);
        } else {
            $this->operandA = $definition;
        }

        return $this;
    }

    protected function mapOpFn($opFn)
    {
        $map = [
            '>' => 'gt',
            '>=' => 'gte',
            '<' => 'lt',
            '<=' => 'lte',
            '=' => 'eq'
        ];
    }
}