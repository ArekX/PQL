<?php

namespace mock;


use ArekX\PQL\Orm\Attributes\Column;
use ArekX\PQL\Orm\Attributes\Relation;
use ArekX\PQL\Orm\Attributes\ViaToModel;
use ArekX\PQL\Orm\Attributes\MultipleRelations;
use ArekX\PQL\Orm\Attributes\Table;

#[Table('profiles')]
class ProfileTableMock
{
    #[Column]
    public int $id;

    #[Column('user_id')]
    public int $userId;

    #[Column]
    public string $name;

    #[Relation(via: 'userId', model: UserTableMock::class, at: 'profile_id', multiple: true)]
    public array $users;
}