<?php

namespace ArekX\PQL\Operators;

use ArekX\PQL\Filter;

/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */
class OperatorGroup extends BaseOperator
{
    const TYPE_BEGIN = 'begin';
    const TYPE_END = 'end';

    public $type = self::TYPE_BEGIN;
    public $name;

    public function extractDefinition(): array
    {
        return parent::extractDefinition() + [
                'type' => $this->type,
                'name' => $this->name
            ];
    }

    public static function fromDefinition(Filter $filter, array $definition): OperatorInterface
    {
        /** @var OperatorGroup $op */
        $op = parent::fromDefinition($filter, $definition);
        $op->type = $definition['type'] ?? self::TYPE_BEGIN;
        $op->name = $definition['name'] ?? null;

        return $op;
    }
}