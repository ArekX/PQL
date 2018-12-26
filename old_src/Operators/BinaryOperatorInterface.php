<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL\Operators;


use ArekX\PQL\Filter;

interface BinaryOperatorInterface
{
    const OPERATOR_IS = 'is';
    const OPERATOR_IN = 'in';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_EQ = 'eq';
    const OPERATOR_GT = 'gt';
    const OPERATOR_LT = 'lt';
    const OPERATOR_GTE = 'gte';
    const OPERATOR_LTE = 'lte';
    const OPERATOR_EXISTS = 'exists';
    const OPERATOR_LIKE = 'like';

    const SEARCH_MIDDLE = 'middle';
    const SEARCH_RIGHT = 'right';
    const SEARCH_LEFT = 'left';
    const SEARCH_USER = 'user';

    /**
     * Inverts operator interface.
     *
     * @return OperatorInterface
     */
    public function not(): BinaryOperatorInterface;

    public function for($definition): BinaryOperatorInterface;

    public function eq($value): Filter;

    public function gt($value): Filter;

    public function lt($value): Filter;

    public function gte($value): Filter;

    public function lte($value): Filter;

    public function is($value): Filter;

    public function in($value): Filter;

    public function find($value, $type = self::SEARCH_MIDDLE): Filter;

    public function exists($value): Filter;

    public function between($fromValue, $toValue): Filter;
}