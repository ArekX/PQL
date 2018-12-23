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

    public function toList($key, $value): QueryableOwnerInterface
    {
        return $this->owner;
    }

    public function indexedBy($key): QueryableOwnerInterface
    {
        return $this->owner;
    }

    public function to($callback): QueryableOwnerInterface
    {
        return $this->owner;
    }
}