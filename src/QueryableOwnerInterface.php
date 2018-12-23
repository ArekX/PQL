<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


use ArekX\PQL\DataSources\DataSourceInterface;

interface QueryableOwnerInterface
{
    public function order(): Order;

    public function map(): Mapper;

    public function fromSource(): DataSourceInterface;
}