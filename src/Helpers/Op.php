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
        return ['compare', $op, $a, $b];
    }

    public static function not($op): array
    {
        return ['not', $op];
    }

    public static function in(...$values): array
    {
        return ['in', count($values) === 1 && is_array($values[0]) ? $values[0] : $values];
    }

    public static function raw(string $expression, array $params = []): array
    {
        return ['raw', $expression, $params];
    }

    public static function like($op, $value): array
    {
        return ['like', $op, $value];
    }

    public static function val($value): array
    {
        return ['val', $value];
    }

    public static function between($op, $from, $to): array
    {
        return ['between', $op, $from, $to];
    }
}