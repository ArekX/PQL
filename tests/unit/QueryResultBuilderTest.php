<?php

namespace tests;

use ArekX\PQL\Contracts\ResultReader;
use ArekX\PQL\QueryResultBuilder;
use mock\ResultReaderMock;

class QueryResultBuilderTest extends \Codeception\Test\Unit
{
    public function testFirst()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);
        $builder = $this->createResultBuilder($reader);

        expect($builder->first())->toBe(['a', 'b']);
        expect($reader->isFinalized)->toBeTrue();
    }

    protected function createResultBuilder(ResultReader $reader)
    {
        $builder = new QueryResultBuilder();
        return $builder->useReader($reader);
    }

    public function testAll()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);
        $builder = $this->createResultBuilder($reader);

        expect($builder->all())->toBe([
            ['a', 'b'],
            ['c', 'd']
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testScalar()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);
        $builder = $this->createResultBuilder($reader);

        expect($builder->scalar())->toBe('a');
        expect($reader->isFinalized)->toBeTrue();
        $reader->reset();
        expect($builder->scalar(1))->toBe('b');
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testColumn()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);
        $builder = $this->createResultBuilder($reader);

        expect($builder->column())->toBe(['a', 'c']);
        expect($reader->isFinalized)->toBeTrue();
        $reader->reset();
        expect($builder->column(1))->toBe(['b', 'd']);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testExists()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);

        $builder = $this->createResultBuilder($reader);

        expect($builder->exists())->toBeTrue();
        expect($reader->isFinalized)->toBeTrue();

        $reader = ResultReaderMock::create([]);

        $builder = $this->createResultBuilder($reader);

        expect($builder->exists())->toBeFalse();
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testListPipeline()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);

        $builder = $this->createResultBuilder($reader);

        expect($builder->pipeListBy('0', '1')->result())->toBe([
            'a' => 'b',
            'c' => 'd'
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testSortPipeline()
    {
        $reader = ResultReaderMock::create([
            ['f', 'd'],
            ['a', 'b'],
            ['c', 'd'],
            ['d', 'd'],
            ['b', 'd'],
        ]);

        $builder = $this->createResultBuilder($reader)
            ->pipeSort(fn($a, $b) => strcmp($a[0], $b[0]));

        expect($builder->result())->toBe([
            ['a', 'b'],
            ['b', 'd'],
            ['c', 'd'],
            ['d', 'd'],
            ['f', 'd'],
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testFilterPipeline()
    {
        $reader = ResultReaderMock::create([
            ['f', 'd'],
            ['a', 'b'],
            ['c', 'd'],
            ['d', 'd'],
            ['b', 'd'],
        ]);

        $builder = $this->createResultBuilder($reader)
            ->pipeFilter(fn($row) => $row[0] != 'c');

        expect($builder->result())->toBe([
            ['f', 'd'],
            ['a', 'b'],
            ['d', 'd'],
            ['b', 'd'],
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testReduce()
    {
        $reader = ResultReaderMock::create([
            ['f', 1],
            ['a', 2],
            ['c', 3],
            ['d', 4],
            ['b', 5],
        ]);

        $builder = $this->createResultBuilder($reader)
            ->pipeReduce(fn($acc, $row) => $acc + $row[1], 0);

        expect($builder->result())->toBe(15);
        expect($reader->isFinalized)->toBeTrue();
    }


    public function testMap()
    {
        $reader = ResultReaderMock::create([
            ['f', 1],
            ['a', 2],
            ['c', 3],
        ]);

        $builder = $this->createResultBuilder($reader)
            ->pipeMap(fn($row) => [
                'key' => $row[0],
                'value' => $row[1]
            ]);

        expect($builder->result())->toBe([
            ['key' => 'f', 'value' => 1],
            ['key' => 'a', 'value' => 2],
            ['key' => 'c', 'value' => 3],
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testIndexBy()
    {
        $reader = ResultReaderMock::create([
            ['id' => 1, 'value' => 'f'],
            ['id' => 5, 'value' => 'a'],
            ['id' => 3, 'value' => 'b'],
            ['id' => 4, 'value' => 'c'],
        ]);

        $builder = $this->createResultBuilder($reader)
            ->pipeIndexBy('id');

        expect($builder->result())->toBe([
            1 => ['id' => 1, 'value' => 'f'],
            5 => ['id' => 5, 'value' => 'a'],
            3 => ['id' => 3, 'value' => 'b'],
            4 => ['id' => 4, 'value' => 'c'],
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }


    public function testClearPipeline()
    {
        $resultSet = [
            ['f', 1],
            ['a', 2],
            ['c', 3],
            ['d', 4],
            ['b', 5],
        ];

        $reader = ResultReaderMock::create($resultSet);

        $builder = $this->createResultBuilder($reader)
            ->pipeReduce(fn($acc, $row) => $acc + $row[1], 0);

        $builder->clearPipeline();

        expect($builder->result())->toBe($resultSet);
        expect($reader->isFinalized)->toBeTrue();
    }

    public function testListWithInvalidKeys()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);

        $builder = $this->createResultBuilder($reader);

        expect(function () use ($builder) {
            $builder->pipeListBy('a', 'b')->result();
        })->callableToThrow(\Exception::class);
    }
}