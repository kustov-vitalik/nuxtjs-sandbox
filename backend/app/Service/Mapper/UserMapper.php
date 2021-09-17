<?php

declare(strict_types=1);


namespace App\Service\Mapper;


use App\Common\Dto\User as UserDto;
use App\Models\User;

class UserMapper
{
    public function toUserDto(User $user): UserDto
    {
        return new UserDto(id: $user->id, name: $user->name, email: $user->email);
    }
}
