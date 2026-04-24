<?php

declare(strict_types=1);

namespace App\Application\User\Mapper;

use App\Domain\User\Entity\UserModel;

final class UserResponseMapper
{
    public function toArray(UserModel $user): array
    {
        return [
            'id' => $user->id()->value(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'role' => $user->role()->value,
            'status' => $user->status()->value,
        ];
    }

    /**
     * @param UserModel[] $users
     */
    public function toArrayList(array $users): array
    {
        return array_map(fn (UserModel $user): array => $this->toArray($user), $users);
    }
}
