<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


class Limiter
{
    /** @var SelectableOwnerInterface */
    protected $owner;

    public function __construct(SelectableOwnerInterface $owner = null)
    {
        $this->owner = $owner;
    }

    public static function as(SelectableOwnerInterface $owner = null)
    {
        return Instance::ensure(static::class, [$owner]);
    }

    public function then(): SelectableOwnerInterface
    {
        return $this->owner;
    }

    public function from($count): Limiter
    {
        return $this;
    }

    public function to($count): Limiter
    {
        return $this;
    }

    public function take($count): Limiter
    {
        return $this;
    }
}