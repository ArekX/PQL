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
    /** @var QueryableOwnerInterface */
    protected $owner;

    public function __construct(QueryableOwnerInterface $owner = null)
    {
        $this->owner = $owner;
    }

    public static function from(QueryableOwnerInterface $owner = null)
    {
        return new static($owner);
    }

    public function then(): QueryableOwnerInterface
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