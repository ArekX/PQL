<?php

namespace tests\Drivers\MySql\Builder\Builders;

use ArekX\PQL\Drivers\MySql\Builder\MySqlQueryBuilder;
use ArekX\PQL\Sql\Query\Delete;

class DeleteBuilderTest extends \Codeception\Test\Unit
{
    public function testBuilder()
    {
        $builder = new MySqlQueryBuilder();

        $q = Delete::create()->from('table');

        expect($builder->build($q)->getQuery())->toBe('DELETE FROM table');
    }
}