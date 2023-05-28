<?php

namespace ArekX\PQL\Orm;

use ArekX\PQL\Contracts\Driver;
use ArekX\PQL\Contracts\QueryBuilder;
use ArekX\PQL\Orm\Attributes\Column;
use ArekX\PQL\Orm\Attributes\MultiRelation;
use ArekX\PQL\Orm\Attributes\Table;
use ReflectionClass;

/**
 * Relational Mapper for mapping
 * models to tables.
 *
 */
class RelationalMapper
{
    /**
     * Current driver in use.
     * @var Driver
     */
    public Driver $driver;

    /**
     * Current query builder in use.
     *
     * @var QueryBuilder
     */
    public QueryBuilder $builder;

    /**
     * Compiler for model structure
     *
     * @var ModelCompiler
     */
    protected ModelCompiler $compiler;

    /**
     * Creates a new instance of this mapper
     *
     * @param Driver|null $driver Driver to be set to be used
     * @param QueryBuilder|null $builder Builder to be used
     * @return static
     */
    public static function create(?Driver $driver = null, ?QueryBuilder $builder = null): static
    {
        return new static($driver, $builder);
    }

    /**
     * Creates new instance of this mapper
     *
     * @param Driver|null $driver Driver to be set to be used
     * @param QueryBuilder|null $builder Builder to be used
     */
    public function __construct(?Driver $driver = null, ?QueryBuilder $builder = null)
    {
        if ($driver) {
            $this->useDriver($driver);
        }

        if ($builder) {
            $this->useBuilder($builder);
        }

        $this->compiler = new ModelCompiler();
    }

    /**
     * Set a driver to be used for queries.
     *
     * @param Driver $driver Driver to be used.
     * @return $this
     */
    public function useDriver(Driver $driver): static
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Set query builder to be used.
     *
     * @param QueryBuilder $builder Builder to be used.
     * @return $this
     */
    public function useBuilder(QueryBuilder $builder): static
    {
        $this->builder = $builder;
        return $this;
    }


    public function toModelStructure(string $modelClass): array
    {
        return $this->compiler->resolve($modelClass);
    }

    public function find(string $modelClass)
    {

    }

    public function save($model)
    {

    }

    public function delete($model)
    {

    }

    public function insertMany(string $modelClass)
    {

    }

    public function updateMany(string $modelClass)
    {

    }

    public function deleteMany(string $modelClass)
    {

    }
}