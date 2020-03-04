<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL\Drivers\ArrayData;

use ArekX\PQL\Contracts\DriverInterface;
use ArekX\PQL\Drivers\ArrayData\Filter\Filter;
use ArekX\PQL\FactoryTrait;
use ArekX\PQL\Query;

class Driver implements DriverInterface
{
    use FactoryTrait;

    protected $filter;
    protected $selector;
    protected $result;
    public $from;

    public function __construct()
    {
        $this->from = From::create($this);
        $this->filter = Filter::create($this);
        $this->selector = Selector::create($this);
        $this->result = Result::create($this);
    }

    public function run(Query $query, Query $parent = null)
    {
        // TODO: Driver needs to be isolated.
        // TODO: No singleton.
        $this->result->useQuery($query);
        $this->selector->useQuery($query);
        $this->from->useQuery($query, $parent);

        while ($this->from->hasNext()) {
            if (!$this->filter->evaluate($query->where)) {
                $this->from->next();
                continue;
            }

            $this->result->add($this->selector->select());

            if ($this->result->isSingleResult()) {
                break;
            }

            $this->from->next();
        }

        return $this->result->resolve();
    }
}