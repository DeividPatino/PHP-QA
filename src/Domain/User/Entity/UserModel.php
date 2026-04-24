<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\Enum\UserRole;
use App\Domain\User\Enum\UserStatus;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;

final class UserModel
{
    public function __construct(
        private UserId $id,
        private UserName $name,
        private UserEmail $email,
        private UserPassword $password,
        private UserRole $role,
        private UserStatus $status
    ) {
    }

    public static function create(
        UserId $id,
        UserName $name,
        UserEmail $email,
        UserPassword $password,
        UserRole $role = UserRole::USER,
        UserStatus $status = UserStatus::ACTIVE
    ): self {
        return new self($id, $name, $email, $password, $role, $status);
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function password(): UserPassword
    {
        return $this->password;
    }

    public function role(): UserRole
    {
        return $this->role;
    }

    public function status(): UserStatus
    {
        return $this->status;
    }

    public function updateName(UserName $name): void
    {
        $this->name = $name;
    }

    public function updateEmail(UserEmail $email): void
    {
        $this->email = $email;
    }

    public function updatePassword(UserPassword $password): void
    {
        $this->password = $password;
    }

    public function updateRole(UserRole $role): void
    {
        $this->role = $role;
    }

    public function updateStatus(UserStatus $status): void
    {
        $this->status = $status;
    }

    public function activate(): void
    {
        $this->status = UserStatus::ACTIVE;
    }

    public function deactivate(): void
    {
        $this->status = UserStatus::INACTIVE;
    }
}
