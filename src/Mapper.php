<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


class Mapper
{
    /** @var SelectableOwnerInterface */
    protected $owner;

    public function __construct(SelectableOwnerInterface $owner = null)
    {
        $this->owner = $owner;
    }

    public static function from(SelectableOwnerInterface $owner = null)
    {
        return new static($owner);
    }

    public function then(): SelectableOwnerInterface
    {
        return $this->owner;
    }

    public function toList($key, $value): Mapper
    {
        return $this;
    }

    public function indexedBy($key): Mapper
    {
        return $this;
    }

    public function to($callback): Mapper
    {
        return $this;
    }
}