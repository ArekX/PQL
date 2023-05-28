<?php

namespace unit\Orm;

use ArekX\PQL\Orm\Attributes\MultiRelation;
use ArekX\PQL\Orm\Attributes\Relation;
use ArekX\PQL\Orm\RelationalMapper;
use mock\MockDriver;
use mock\ProfileTableMock;
use mock\QueryBuilderMock;
use mock\UserTableMock;

class RelationalMapperTest extends \Codeception\Test\Unit
{
    public function testModelStructureResolution()
    {
        $driver = new MockDriver();
        $builder = new QueryBuilderMock();

        $orm = RelationalMapper::create($driver, $builder);

        $structure = $orm->toModelStructure(UserTableMock::class);

        $expected = [
            'table' => 'users',
            'columns' => [
                'id' => 'id',
                'username' => 'username',
                'password' => 'password',
                'profile_id' => 'profileId'
            ],
            'relations' => [
                'profiles' => [
                    'model' => ProfileTableMock::class,
                    'foreign_key' => 'profile_id',
                    'table_key' => 'id',
                    'multiple_results' => true,
                    'table' => 'profiles',
                ]
            ]
        ];

        expect($structure)->toBe($expected);
    }
}