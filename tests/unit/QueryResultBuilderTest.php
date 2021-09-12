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

       public function testList()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);

        $builder = $this->createResultBuilder($reader);

        expect($builder->list(0, 1))->toBe([
            'a' => 'b',
            'c' => 'd'
        ]);
        expect($reader->isFinalized)->toBeTrue();
    }

         public function testListWithInvalidKeys()
    {
        $reader = ResultReaderMock::create([
            ['a', 'b'],
            ['c', 'd']
        ]);

        $builder = $this->createResultBuilder($reader);

        expect(function() use($builder) {
            $builder->list('a', 'b');
        })->callableToThrow(\Exception::class);
    }

    protected function createResultBuilder(ResultReader $reader)
    {
        $builder = new QueryResultBuilder();
        return $builder->useReader($reader);
    }
}