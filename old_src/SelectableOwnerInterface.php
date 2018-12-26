<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


use ArekX\PQL\DataSources\DataSourceInterface;

interface SelectableOwnerInterface
{
    public function order(): Order;

    public function map(): Mapper;

    public function reduce(): Reducer;

    public function limit(): Limiter;

    public function join(): Join;

    public function fromSource(): DataSourceInterface;
}