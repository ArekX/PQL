<?php

namespace mock;


use ArekX\PQL\Orm\Attributes\Column;
use ArekX\PQL\Orm\Attributes\MultiRelation;
use ArekX\PQL\Orm\Attributes\Relation;
use ArekX\PQL\Orm\Attributes\Table;

#[Table('users')]
class UserTableMock
{
    #[Column]
    public int $id;

    #[Column]
    public string $username;

    #[Column]
    public string $password;

    #[Column('profile_id')]
    public int $profileId;

    #[Relation(via: 'profileId', model: ProfileTableMock::class, at: 'id', multiple: true)]
    public array $profiles;
}