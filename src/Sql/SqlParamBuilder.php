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

namespace ArekX\PQL\Sql;

use ArekX\PQL\Contracts\ParamsBuilder;

/**
 * Represents a class for building parameters
 * for a SQL query.
 */
class SqlParamBuilder implements ParamsBuilder
{
    /**
     * Prefix to be used when wrapping values into
     * parameters.
     *
     * @see SqlParamBuilder::wrapValue()
     * @var string
     */
    public $prefix = ':t';

    /**
     * List of parameters set.
     * @var array
     */
    protected $parameters = [];

    /**
     * Index of parameter used in wrapValue.
     *
     * @see SqlParamBuilder::wrapValue()
     * @var int
     */
    protected $parameterIndex = 0;

    /**
     * @inheritDoc
     */
    public function wrapValue($value, $type = null)
    {
        $key = $this->prefix . $this->parameterIndex++;
        $this->add($key, $value, $type);
        return $key;
    }

    /**
     * @inheritDoc
     */
    public function add($key, $value, $type = null): void
    {
        $this->parameters[$key] = [$value, $type];
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->parameters)) {
            return null;
        }

        return $this->parameters[$key];
    }

    /**
     * @inheritDoc
     */
    public function build(): array
    {
        return $this->parameters;
    }
}