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

namespace ArekX\PQL\Sql\Query\Traits;

use ArekX\PQL\Query;

trait ConfigureTrait
{
    /**
     * Set a query specific configuration
     *
     * This configuration can be used to pass query specific
     * configuration to the builder or the driver in use.
     *
     * @param array $config
     * @return $this
     */
    public function config(array $config)
    {
        $this->use('config', $config);
        return $this;
    }
}