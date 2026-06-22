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

namespace integration\Pdo;

use ArekX\PQL\QueryRunner;
use PDO;

use function ArekX\PQL\Sql\all;
use function ArekX\PQL\Sql\column;
use function ArekX\PQL\Sql\insert;
use function ArekX\PQL\Sql\search;
use function ArekX\PQL\Sql\select;

/**
 * Shared integration tests for the security fixes, run against every dialect.
 *
 * Each dialect's driver test uses this trait so the same behaviour is checked
 * against a real MySQL, PostgreSQL, SQL Server and SQLite database. The tests
 * assert on returned rows rather than generated SQL so they stay dialect
 * agnostic, and they use the `users` table loaded by the dialect test case.
 */
trait SecurityFixesTrait
{
    /**
     * Fix #1: identifiers are quoted, so a reserved word used as an alias works
     * instead of producing a syntax error.
     */
    public function testReservedWordIdentifierIsQuoted()
    {
        $runner = QueryRunner::create($this->createDriver(), $this->createQueryBuilder());

        $row = $runner->fetchFirst(
            select(['order' => 'username'])->from('users')->orderBy(['id' => 'asc'])
        );

        expect($row['order'])->toBe('john');
    }

    /**
     * Fix #2: the ORDER BY direction is honoured for both asc and desc.
     */
    public function testOrderByDirectionAscAndDesc()
    {
        $runner = QueryRunner::create($this->createDriver(), $this->createQueryBuilder());

        $asc = $runner->fetchFirst(select('username')->from('users')->orderBy(['id' => 'asc']));
        $desc = $runner->fetchFirst(select('username')->from('users')->orderBy(['id' => 'desc']));

        expect($asc['username'])->toBe('john');
        expect($desc['username'])->toBe('wall');
    }

    /**
     * Fix #3: a value-bearing sub query shares the outer query's parameters, so
     * the outer value and the sub query value are both bound and the result is
     * correct. Before the fix this gave a wrong result or a binding error.
     */
    public function testSubQueryValuesShareOuterParameters()
    {
        $runner = QueryRunner::create($this->createDriver(), $this->createQueryBuilder());

        $sub = select('id')->from('users')->where(all(['username' => 'jane']));

        $query = select('username')
            ->from('users')
            ->where(all(['password' => 'doe']))
            ->andWhere(['in', column('id'), $sub]);

        $rows = $runner->fetchAll($query);

        expect(array_column($rows, 'username'))->toBe(['jane']);
    }

    /**
     * Fix #4: a fetch mode passed through the config actually takes effect.
     */
    public function testFetchModeFromConfigIsApplied()
    {
        $driver = $this->createDriver(['fetchMode' => PDO::FETCH_NUM]);
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $row = $runner->fetchFirst(select('username')->from('users')->orderBy(['id' => 'asc']));

        // FETCH_NUM gives a numerically indexed row, not an associative one.
        expect(array_key_exists(0, $row))->toBeTrue();
        expect(array_key_exists('username', $row))->toBeFalse();
    }

    /**
     * Fix #4: connection options passed through the config reach the PDO
     * instance. ATTR_CASE is honoured by PDO for every driver, so it is a
     * portable way to prove the options were not dropped.
     */
    public function testOptionsFromConfigAreApplied()
    {
        $driver = $this->createDriver(['options' => [PDO::ATTR_CASE => PDO::CASE_UPPER]]);
        $runner = QueryRunner::create($driver, $this->createQueryBuilder());

        $row = $runner->fetchFirst(select('username')->from('users')->orderBy(['id' => 'asc']));

        expect(array_key_exists('USERNAME', $row))->toBeTrue();
    }

    /**
     * Fix #5: LIKE wildcards in a search value are escaped, so searching for the
     * literal text "100%" matches only "100%" and not "100abc".
     */
    public function testSearchEscapesLikeWildcards()
    {
        $runner = QueryRunner::create($this->createDriver(), $this->createQueryBuilder());

        foreach (['100%', '100abc'] as $name) {
            $runner->run(insert('users', [
                'name' => $name,
                'username' => 'wildcard',
                'password' => 'x',
                'created_at' => '2021-01-01 00:00:00',
            ]));
        }

        $rows = $runner->fetchAll(
            select('name')->from('users')->where(search(column('name'), '100%'))
        );

        expect(array_column($rows, 'name'))->toBe(['100%']);
    }
}
