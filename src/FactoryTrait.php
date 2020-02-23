<?php
/**
 * @author Aleksandar Panic
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @since 1.0.0
 **/

namespace ArekX\PQL;


trait FactoryTrait
{
    public static function create(...$params): self
    {
        return Factory::from(static::class, $params);
    }
}