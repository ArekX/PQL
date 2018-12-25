<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


class Order
{
    /** @var SelectableOwnerInterface */
    protected $owner;

    public function __construct(SelectableOwnerInterface $owner = null)
    {
        $this->owner = $owner;
    }

    public static function from(SelectableOwnerInterface $owner = null)
    {
        return Instance::ensure(static::class, [$owner]);
    }

    public function then(): SelectableOwnerInterface
    {
        return $this->owner;
    }

    public function ascBy($value): Order
    {
        return $this;
    }

    public function descBy($value): Order
    {
        return $this;
    }
}