<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;

use ArekX\PQL\Operators\BinaryOperatorInterface;
use ArekX\PQL\Operators\BinaryOperator;
use ArekX\PQL\Operators\OperatorGroup;
use ArekX\PQL\Operators\OperatorInterface;
use ArekX\PQL\Values\ValueInterface;

class Filter
{
    /** @var OperatorInterface[] */
    protected $list = [];

    /** @var SelectableOwnerInterface */
    protected $owner;

    public static function from(?SelectableOwnerInterface $owner = null, ?array $definition = null)
    {
        return Factory::matchClass(self::class, [$owner, $definition]);
    }

    public function __construct(?SelectableOwnerInterface $owner = null, ?array $definition = null)
    {
        $this->owner = $owner;

        if ($definition) {
            $this->define($definition);
        }
    }

    public function then(): SelectableOwnerInterface
    {
        return $this->owner;
    }

    /**
     * @param null $definition
     * @return OperatorInterface
     */
    public function and($definition = null): BinaryOperatorInterface
    {
        return $this->addBinaryOperator(BinaryOperator::BINARY_AND, $definition);
    }

    /**
     * @param null $definition
     * @return OperatorInterface
     */
    public function or($definition = null): BinaryOperatorInterface
    {
        return $this->addBinaryOperator(BinaryOperator::BINARY_OR, $definition);
    }

    /**
     * @param null $definition
     * @return OperatorInterface
     */
    public function xor($definition = null): BinaryOperatorInterface
    {
        return $this->addBinaryOperator(BinaryOperator::BINARY_XOR, $definition);
    }

    /**
     * @param null $name
     * @return Filter
     */
    public function begin($name = null): Filter
    {
        return $this->addOperatorGroup(OperatorGroup::TYPE_BEGIN, $name);
    }

    /**
     * @param null $name
     * @return Filter
     */
    public function end($name = null): Filter
    {
        return $this->addOperatorGroup(OperatorGroup::TYPE_END, $name);
    }

    public function define(array $definition): Filter
    {
        foreach ($definition as $item) {

            if (is_array($item)) {
                [$opFn, $params] = $item;
                $params = [$params];
            } else {
                $opFn = $item;
                $params = [];
            }

            ([$this, $opFn])(...$params);
        }

        return $this;
    }

    public function dropGroup($name): Filter
    {
        $remove = false;
        foreach ($this->list as $listIndex => $value) {
            if ($value instanceof OperatorGroup && $value->name === $name) {
                $remove = !$remove;
                unset($this->list[$listIndex]);
                continue;
            }

            if ($remove) {
                unset($this->list[$listIndex]);
            }
        }

        $this->list = array_values($this->list);

        return $this;
    }

    public function extractDefinitions()
    {
        return array_map(function (OperatorInterface $item) {
            return $item->extractDefinition();
        }, $this->list);
    }


    protected function addBinaryOperator($type, $definition = null)
    {
        /** @var BinaryOperator $operator */
        $operator = BinaryOperator::create($this, $definition);
        $operator->type = $type;
        $this->list[] = $operator;
        return $operator;
    }

    protected function addOperatorGroup($type, $name = null)
    {
        /** @var OperatorGroup $operator */
        $operator = OperatorGroup::create($this);
        $operator->type = $type;
        $operator->name = $name;
        $this->list[] = $operator;
        return $this;
    }
}