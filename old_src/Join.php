<?php
/**
 * Created by Aleksandar Panic
 * Date: 23-Dec-18
 * Time: 23:41
 * License: MIT
 */

namespace ArekX\PQL;


use ArekX\PQL\DataSources\DataSourceInterface;

class Join implements SelectableOwnerInterface
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

    public function left(): Join
    {
        return $this;
    }

    public function with($value): Join
    {
        return $this;
    }

    public function on($definition = null): Filter
    {
        return Filter::from($this, $definition);
    }

    public function order(): Order
    {
        // TODO: Implement order() method.
    }

    public function map(): Mapper
    {
        // TODO: Implement map() method.
    }

    public function reduce(): Reducer
    {
        // TODO: Implement reduce() method.
    }

    public function limit(): Limiter
    {
        // TODO: Implement limit() method.
    }

    public function fromSource(): DataSourceInterface
    {
        // TODO: Implement fromSource() method.
    }

    public function join(): Join
    {
        // TODO: Implement join() method.
    }
}