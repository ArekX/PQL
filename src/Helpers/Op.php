<?php

namespace ArekX\PQL\Helpers;

class Op
{
    public static function and(...$ops): array
    {
        return ['and', ...$ops];
    }

    public static function or(...$ops): array
    {
        return ['or', ...$ops];
    }

    public static function multi(array $a): array
    {
        return ['multi', $a];
    }

    public static function search(array $a): array
    {
        return ['search', $a];
    }

    public static function filteredSearch(array $search): array
    {
        $result = [];

        foreach ($search as $key => $value) {
            if ($value === '' || $value === null || $value === []) {
                continue;
            }

            $result[$key] = $value;
        }

        return Op::search($result);
    }

    public static function compare($a, $op, $b): array
    {
        if (is_string($a)) {
            $a = static::column($a);
        }

        return ['compare', $op, $a, $b];
    }

    public static function equals($a, $b): array
    {
        return Op::compare($a, '=', $b);
    }

    public static function notEquals($a, $b): array
    {
        return Op::compare($a, '<>', $b);
    }

    public static function greaterThan($a, $b): array
    {
        return Op::compare($a, '>', $b);
    }

    public static function lessThan($a, $b): array
    {
        return Op::compare($a, '<', $b,);
    }

    public static function greaterEquals($a, $b): array
    {
        return Op::compare($a, '>=', $b);
    }

    public static function lessEquals($a, $b): array
    {
        return Op::compare($a, '<=', $b);
    }

    public static function column($op): array
    {
        return ['column', $op];
    }

    public static function not($op): array
    {
        return ['not', $op];
    }

    public static function in($column, $values): array
    {
        if (is_string($column)) {
            $column = static::column($column);
        }

        return ['in', $column, Op::value($values)];
    }

    public static function like($op, $value): array
    {
        if (is_string($op)) {
            $op = Op::column($op);
        }

        return ['like', $op, $value];
    }

    public static function likeSearch($op, string $value): array
    {
        if (is_string($op)) {
            $op = Op::column($op);
        }

        return ['like', $op, Op::value('%' . $value . '%')];
    }

    public static function value($value): array
    {
        return ['value', $value];
    }

    public static function between($expression, $from, $to): array
    {
        if (is_string($expression)) {
            $expression = Op::column($expression);
        }

        return ['between', $expression, $from, $to];
    }

    public static function exists($value): array
    {
        return ['exists', $value];
    }
}