<?php
/**
 * Created by Aleksandar Panic
 * Date: 2020-03-04
 * Time: 17:49
 * License: MIT
 */

namespace ArekX\PQL\Drivers\ArrayData;


use ArekX\PQL\FactoryTrait;
use ArekX\PQL\Query;

class Result
{
    use FactoryTrait;

    /** @var Query */
    protected $query;

    /** @var mixed */
    protected $result;

    /** @var callable */
    protected $resolver;

    /** @var bool */
    protected $singleResult;

    public function useQuery(Query $query)
    {
        $this->query = $query;
        $this->singleResult = false;
        $this->result = [];

        if ($this->query->returnAs === Query::AS_SCALAR) {
            $this->useScalar();
        } elseif ($this->query->returnAs === Query::AS_SINGLE) {
            $this->useSingle();
        } elseif ($this->query->returnAs === Query::AS_COUNT) {
            $this->useCount();
        }
    }

    public function isSingleResult(): bool
    {
        return $this->singleResult;
    }

    public function add($result): void
    {
        if ($this->query->returnAs === Query::AS_ALL) {
            $this->addAll($result);
        } elseif ($this->query->returnAs === Query::AS_COLUMN) {
            $this->addColumn($result);
        } elseif ($this->query->returnAs === Query::AS_SCALAR) {
            $this->addScalar($result);
        } elseif ($this->query->returnAs === Query::AS_MAP) {
            $this->addMap($result);
        } elseif ($this->query->returnAs === Query::AS_SINGLE) {
            $this->addSingle($result);
        } elseif ($this->query->returnAs === Query::AS_COUNT) {
            $this->addCount();
        }
    }

    public function resolve()
    {
        return $this->result;
    }

    public function addAll($result)
    {
        $this->result[] = $result;
    }

    public function addColumn($result)
    {
        $this->result[] = Value::get($result, $this->resolveKey());
    }

    public function addScalar($result)
    {
        $this->result = Value::get($result, $this->resolveKey());
    }

    public function addMap($result)
    {
        $this->result[$this->query->returnAsConfig['key']] = Value::get($result, $this->query->returnAsConfig['value']);
    }

    public function addSingle($result)
    {
        $this->result = $result;
    }

    public function addCount()
    {
        $this->result++;
    }

    protected function useScalar(): void
    {
        $this->result = null;
        $this->singleResult = true;
    }

    protected function useSingle(): void
    {
        $this->singleResult = true;
    }

    protected function useCount(): void
    {
        $this->result = 0;
    }

    protected function resolveKey()
    {
        if ($this->query->returnAsConfig['key'] !== null) {
            return $this->query->returnAsConfig['key'];
        }

        if (!is_string($this->query->select)) {
            throw new \Exception(
                'When retrieving as column, you must either define select as string or set key in:' . $this->query->returnAs
            );
        }

        return $this->query->select;
    }
}