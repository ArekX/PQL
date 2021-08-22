<?php

namespace tests\Drivers\MySql\Builder;

use ArekX\PQL\Drivers\MySql\Builder\Builders\DeleteBuilder;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilderState;
use ArekX\PQL\Sql\Query\Delete;
use ArekX\PQL\Sql\SqlParamBuilder;
use mock\QueryBuilderMock;
use mock\QueryMock;

class MySqlQueryBuilderTest extends \Codeception\Test\Unit
{
    public function testGettingABuilder()
    {
        $s = new MySqlQueryBuilder();

        expect($s->getBuilder(Delete::create()))->toBeInstanceOf(DeleteBuilder::class);
    }

    public function testUnknownQueryShouldThrowAnError()
    {
        $s = new MySqlQueryBuilder();

        $this->expectException(\Exception::class);
        expect($s->getBuilder(QueryMock::create()));
    }

    public function testCreateState()
    {
        $mock = new QueryBuilderMock();

        $mock->query = 'Result query';
        $mock->params = [
            ':key' => 'Value'
        ];

        $s = $this->make(MySqlQueryBuilder::class, [
            'createBuilder' => fn() => $mock
        ]);

        $s->build(Delete::create());

        /** @var MySqlQueryBuilderState $state */
        $state = $mock->lastState;

        expect($state)->toBeInstanceOf(MySqlQueryBuilderState::class);
        expect($state->getParentBuilder())->toBe($s);
        expect($state->getParamsBuilder())->toBeInstanceOf(SqlParamBuilder::class);
    }
}