<?php

namespace ArekX\PQL\Operators;

use ArekX\PQL\Filter;

/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */
class BinaryOperator extends BaseBinaryOperator
{
    const BINARY_AND = 'and';
    const BINARY_OR = 'or';
    const BINARY_XOR = 'xor';

    public $type;

    public static function fromDefinition(Filter $filter, array $definition): OperatorInterface
    {
        /** @var static $op */
        $op = parent::fromDefinition($filter, $definition);
        $op->type = $definition['type'] ?? self::BINARY_AND;

        return $op;
    }

    public function extractDefinition(): array
    {
        return parent::extractDefinition() + [
                'type' => $this->type
            ];
    }
}