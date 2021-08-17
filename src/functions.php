<?php

namespace ArekX\PQL;

use ArekX\PQL\Helpers\Op;

if (!function_exists('\ArekX\PQL\select')) {
    function select($columns) {
        return Select::create()->columns($columns);
    }
}

if (!function_exists('\ArekX\PQL\insert')) {
    function insert($into, $values) {
        return Insert::into($into, $values);
    }
}

if (!function_exists('\ArekX\PQL\delete')) {
    function delete($from, $where = null) {
        return Delete::fromTable($from)->where($where);
    }
}

if (!function_exists('\ArekX\PQL\update')) {
    function update($tableExpression, $values, $condition = null) {
        return Update::item($tableExpression, $values)->where($condition);
    }
}

if (!function_exists('\ArekX\PQL\raw')) {
    function raw($query, $params = []) {
        return Raw::query($query, $params);
    }
}

if (!function_exists('\ArekX\PQL\union')) {
    function union($initial) {
        return Union::create($initial);
    }
}

if (!function_exists('\ArekX\PQL\col')) {
    function column($column) {
        return Op::column($column);
    }
}

if (!function_exists('\ArekX\PQL\val')) {
    function value($value) {
        return Op::value($value);
    }
}

if (!function_exists('\ArekX\PQL\equals')) {
    function equals($left, $right) {
        return Op::equals($left, $right);
    }
}

if (!function_exists('\ArekX\PQL\notEquals')) {
    function notEquals($left, $right) {
        return Op::notEquals($left, $right);
    }
}

if (!function_exists('\ArekX\PQL\greaterThan')) {
    function greaterThan($left, $right) {
        return Op::greaterThan($left, $right);
    }
}

if (!function_exists('\ArekX\PQL\lessThan')) {
    function lessThan($left, $right) {
        return Op::lessThan($left, $right);
    }
}

if (!function_exists('\ArekX\PQL\greaterEquals')) {
    function greaterEquals($left, $right) {
        return Op::greaterEquals($left, $right);
    }
}

if (!function_exists('\ArekX\PQL\lessEquals')) {
    function lessEquals($left, $right) {
        return Op::lessEquals($left, $right);
    }
}

if (!function_exists('\ArekX\PQL\compare')) {
    function compare($left, $op, $right) {
        return Op::compare($left, $op, $right);
    }
}

if (!function_exists('\ArekX\PQL\between')) {
    function between($expression, $from, $to) {
        return Op::between($expression, $from, $to);
    }
}

if (!function_exists('\ArekX\PQL\exists')) {
    function exists($expression) {
        return Op::exists($expression);
    }
}


if (!function_exists('\ArekX\PQL\multi')) {
    function multi($assoc) {
        return Op::multi($assoc);
    }
}

if (!function_exists('\ArekX\PQL\search')) {
    function search($assoc) {
        return Op::search($assoc);
    }
}

if (!function_exists('\ArekX\PQL\like')) {
    function like($left, $value) {
        return Op::like($left, $value);
    }
}

if (!function_exists('\ArekX\PQL\likeSearch')) {
    function likeSearch($left, $term) {
        return Op::likeSearch($left, $term);
    }
}

if (!function_exists('\ArekX\PQL\filteredSearch')) {
    function filteredSearch($items) {
        return Op::filteredSearch($items);
    }
}

if (!function_exists('\ArekX\PQL\andOp')) {
    function andOp(...$items) {
        return Op::and($items);
    }
}

if (!function_exists('\ArekX\PQL\orOp')) {
    function orOp(...$items) {
        return Op::or($items);
    }
}

if (!function_exists('\ArekX\PQL\notOp')) {
    function notOp($expression) {
        return Op::not($expression);
    }
}