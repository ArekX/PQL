<?php

namespace tests;

use ArekX\PQL\QueryRunner;
use mock\MockDriver;
use mock\QueryBuilderMock;
use function ArekX\PQL\Sql\select;

class QueryRunnerTest extends \Codeception\Test\Unit
{
    public function testRun()
    {
        expect($this->create([], 1)->run(select('*')))->toBe(1);
        expect($this->create([], 0)->run(select('*')))->toBe(0);
    }

    public function testFetchFirst()
    {
        expect($this->create([
            ['a', 'b'],
            ['c', 'd']
        ])->fetchFirst(select('*')))->toBe(['a', 'b']);
    }

    public function testFetchAll()
    {
        $results = [
            ['a', 'b'],
            ['c', 'd'],
            ['d', 'f'],
        ];

        expect($this->create($results)->fetchAll(select('*')))->toBe($results);
    }

    public function testFetchBuilder()
    {
        $results = [
            ['a', 'b'],
            ['c', 'd'],
            ['d', 'f'],
        ];

        expect($this->create($results)->fetch(select('*'))->result())->toBe($results);
    }

    public function testFetchReader()
    {
        $results = [
            ['a', 'b'],
            ['c', 'd'],
            ['d', 'f'],
        ];

        expect($this->create($results)->fetchReader(select('*'))->getAllRows())->toBe($results);
    }

    protected function create(array $results, $runResult = 1)
    {
        return QueryRunner::create(MockDriver::for($results, $runResult), QueryBuilderMock::create());
    }
}