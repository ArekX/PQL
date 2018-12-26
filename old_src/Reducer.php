<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


class Reducer
{
    /** @var SelectableOwnerInterface */
    protected $owner;

    public function __construct(SelectableOwnerInterface $owner = null)
    {
        $this->owner = $owner;
    }

    public static function from(SelectableOwnerInterface $owner = null)
    {
        return Factory::matchClass(self::class, [$owner]);
    }

    public function then(): SelectableOwnerInterface
    {
        return $this->owner;
    }

    public function toCount($byName = null): Reducer
    {
        return $this;
    }

    public function toSum($byName = null): Reducer
    {
        return $this;
    }

    public function toAverage($byName = null): Reducer
    {
        return $this;
    }

    public function toMaxValue($byName = null): Reducer
    {
        return $this;
    }

    public function toMinValue($byName = null): Reducer
    {
        return $this;
    }

    public function to($callback, $initial = null): Reducer
    {
        return $this;
    }
}