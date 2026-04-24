<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Mapper;

use App\Domain\User\Entity\UserModel;
use App\Domain\User\Enum\UserRole;
use App\Domain\User\Enum\UserStatus;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;

final class UserPdoMapper
{
    public function toDomain(array $row): UserModel
    {
        return UserModel::create(
            new UserId($row['id']),
            new UserName($row['name']),
            new UserEmail($row['email']),
            UserPassword::fromHashed($row['password']),
            UserRole::from($row['role']),
            UserStatus::from($row['status'])
        );
    }

    public function toRow(UserModel $user): array
    {
        return [
            'id' => $user->id()->value(),
            'name' => $user->name()->value(),
            'email' => $user->email()->value(),
            'password' => $user->password()->value(),
            'role' => $user->role()->value,
            'status' => $user->status()->value,
        ];
    }
}
