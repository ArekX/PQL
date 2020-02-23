<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-02-23
 * Time: 13:26
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData\Contracts;


use ArekX\PQL\Drivers\ArrayData\Filter\Filter;

interface OperatorInterface
{
    public function __construct(Filter $filter);

    public function match($rule);

    public function parse($rule);

    public function evaluate($rule);
}