<?php

namespace ArekX\PQL\Orm;

use ArekX\PQL\Orm\Attributes\Column;
use ArekX\PQL\Orm\Attributes\MultiRelation;
use ArekX\PQL\Orm\Attributes\Relation;
use ArekX\PQL\Orm\Attributes\Table;
use ArekX\PQL\Orm\Attributes\ViaToModel;
use ReflectionClass;
use ReflectionProperty;

class ModelCompiler
{
    protected $compiled = [];

    public function resolve(string $modelClass): array
    {
        if (!empty($this->compiled[$modelClass])) {
            return $this->compiled[$modelClass];
        }

        $resolvedModel = [];

        $reflected = new ReflectionClass($modelClass);

        $resolvedModel['table'] = $reflected->getAttributes(Table::class)[0]->getArguments()[0];

        $properties = $reflected->getProperties(ReflectionProperty::IS_PUBLIC);

        $resolvedModel['columns'] = $this->resolveColumns($properties);

        $propertyColumnMap = array_flip($resolvedModel['columns']);

        $resolvedModel['relations'] = $this->resolveRelations($properties, $propertyColumnMap);

        $this->compiled[$modelClass] = $resolvedModel;

        foreach ($this->compiled[$modelClass]['relations'] as &$config) {
            $config['table'] = $this->resolve($config['model'])['table'];
        }

        return $this->compiled[$modelClass];
    }

    protected function resolveColumns(array $properties): array
    {
        $columns = [];

        foreach ($properties as $property) {
            $column = $property->getAttributes(Column::class);
            $propertyName = $property->getName();

            if (!empty($column)) {
                $column = $column[0]->getArguments();

                if (empty($column)) {
                    $column = $propertyName;
                } else {
                    $column = $column[0];
                }

                $columns[$column] = $propertyName;
            }
        }

        return $columns;
    }

    protected function resolveRelations(array $properties, array $propertyColumnMap): array
    {
        $relations = [];

        foreach ($properties as $property) {
            $relation = $property->getAttributes(Relation::class);

            if (empty($relation)) {
                continue;
            }

            $propertyName = $property->getName();

            [
                'via' => $via,
                'model' => $model,
                'at' => $at,
                'multiple' => $multipleResults
            ] = $relation[0]->getArguments();

            $relations[$propertyName] = [
                'model' => $model,
                'foreign_key' => $propertyColumnMap[$via],
                'table_key' => $at,
                'multiple_results' => $multipleResults
            ];

            $viaToModel = $property->getAttributes(ViaToModel::class);
        }

        return $relations;
    }
}