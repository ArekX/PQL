<?php
/**
 * Copyright 2021 Aleksandar Panic
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace ArekX\PQL\Sql\Statement\Traits;

use ArekX\PQL\Contracts\StructuredQuery;

trait MethodStatementTrait
{
    /**
     * Creates new instance of this method with values set.
     *
     * @param string|StructuredQuery $name Name of the stored procedure
     * @param array|StructuredQuery ...$params
     * @return $this
     */
    public static function as(StructuredQuery|string $name, ...$params): static
    {
        return static::create()->name($name)->params($params);
    }

    /**
     * Name of the method.
     *
     * @param string|StructuredQuery $name
     * @return $this
     */
    public function name(StructuredQuery|string $name): static
    {
        $this->use('name', $name);
        return $this;
    }

    /**
     * Set params of the method.
     *
     * @param array[]|StructuredQuery|null $params
     * @return $this
     */
    public function params(array|StructuredQuery|null $params): static
    {
        $this->use('params', $params);
        return $this;
    }

    /**
     * Add a parameter to the parameters list of the method.
     *
     * @param array|StructuredQuery $param
     * @return $this
     */
    public function addParam(array|StructuredQuery $param): static
    {
        $this->append('params', $param);
        return $this;
    }
}
