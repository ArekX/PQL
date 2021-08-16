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

    public static function compare($a, $b, $op = '='): array
    {
        if (is_string($a)) {
            $a = static::col($a);
        }

        return ['compare', $op, $a, $b];
    }

    public static function col($op): array
    {
        return ['column', $op];
    }

    public static function not($op): array
    {
        return ['not', $op];
    }

    public static function in($column, ...$values): array
    {
        if (is_string($column)) {
            $column = static::col($column);
        }
        return ['in', $column, count($values) === 1 && is_array($values[0]) ? $values[0] : $values];
    }

    public static function like($op, $value): array
    {
        if (is_string($op)) {
            $op = Op::col($op);
        }

        return ['like', $op, $value];
    }

    public static function likeSearch($op, string $value): array
    {
        if (is_string($op)) {
            $op = Op::col($op);
        }

        return ['like', $op, Op::val('%' . $value . '%')];
    }

    public static function val($value): array
    {
        return ['value', $value];
    }

    public static function between($expression, $from, $to): array
    {
        if (is_string($expression)) {
            $expression = Op::col($expression);
        }

        return ['between', $expression, $from, $to];
    }

    public static function exists($value): array
    {
        return ['exists', $value];
    }
}