<?php

/**
 * Copyright 2026 Aleksandar Panic
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

namespace ArekX\PQL\Drivers\Pdo\Common\Builders\Traits;

use ArekX\PQL\Drivers\Pdo\Common\CommonQueryBuilderState;

trait QuoteNameTrait
{
    /**
     * Quotes an identifier using the quote characters configured on the state.
     *
     * The quote characters are dialect specific and are read from the query
     * builder state. The name is split on `.` into segments (e.g.
     * `schema.table.column`) and each segment is quoted independently.
     *
     * Safety contract: this method always emits a single inert, fully quoted
     * identifier (or throws). Each segment is escaped by doubling the dialect
     * closing quote character (`` ` `` -> ` `` `, `"` -> `""`, `]` -> `]]`),
     * which is the only character able to terminate a quoted identifier. There
     * is therefore no input that can break out of the identifier context, in
     * any dialect.
     *
     * Normalization: each segment is trimmed of surrounding spaces as a
     * convenience for forgiving array/CSV-shaped input. This is a documented
     * normalization, not the safety mechanism (escaping is). Only the ASCII
     * space is trimmed so that control characters (including those PHP's
     * default trim() would strip, e.g. NUL/tab/newline) are never silently
     * removed; a segment that is empty after trimming, or that contains
     * control characters, is rejected rather than silently mutated.
     *
     * The literal `*` (and `table.*`) is preserved unquoted.
     *
     * Note: because `.` is the segment separator, an identifier that literally
     * contains a dot cannot be expressed through this dotted-string form; pass
     * such a segment on its own instead.
     *
     * @param string $name Identifier to be quoted.
     * @param CommonQueryBuilderState $state Query builder state holding the quote characters.
     * @return string
     * @throws \InvalidArgumentException If a segment is empty or contains control characters.
     */
    protected function quoteName(string $name, CommonQueryBuilderState $state): string
    {
        $opening = $state->getQuoteCharacter();
        $closing = $state->getClosingQuoteCharacter();

        $quoted = [];
        foreach (explode('.', $name) as $segment) {
            $segment = trim($segment, ' ');

            if ($segment === '*') {
                $quoted[] = '*';
                continue;
            }

            if ($segment === '' || preg_match('/[\x00-\x1F]/', $segment)) {
                throw new \InvalidArgumentException(
                    'Invalid identifier segment: ' . var_export($segment, true)
                );
            }

            $quoted[] = $opening . str_replace($closing, $closing . $closing, $segment) . $closing;
        }

        return implode('.', $quoted);
    }
}
