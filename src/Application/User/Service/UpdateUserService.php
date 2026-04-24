<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Command\UpdateUserCommand;
use App\Application\User\Port\UserRepositoryPort;
use App\Domain\User\Entity\UserModel;
use App\Domain\User\Enum\UserRole;
use App\Domain\User\Enum\UserStatus;
use App\Domain\User\Exception\InvalidUserRoleException;
use App\Domain\User\Exception\InvalidUserStatusException;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;

final class UpdateUserService
{
    public function __construct(private readonly UserRepositoryPort $repository)
    {
    }

    public function handle(UpdateUserCommand $command): UserModel
    {
        $user = $this->repository->findById($command->id);

        if ($user === null) {
            throw new UserNotFoundException('User not found.');
        }

        if ($command->name !== null) {
            $user->updateName(new UserName($command->name));
        }

        if ($command->email !== null) {
            $existing = $this->repository->findByEmail($command->email);

            if ($existing !== null && $existing->id()->value() !== $user->id()->value()) {
                throw new UserAlreadyExistsException('Email is already registered.');
            }

            $user->updateEmail(new UserEmail($command->email));
        }

        if ($command->password !== null) {
            $user->updatePassword(UserPassword::fromPlain($command->password));
        }

        if ($command->role !== null) {
            try {
                $user->updateRole(UserRole::from($command->role));
            } catch (\ValueError) {
                throw new InvalidUserRoleException('Invalid role. Allowed values: admin, user.');
            }
        }

        if ($command->status !== null) {
            try {
                $status = UserStatus::from($command->status);
            } catch (\ValueError) {
                throw new InvalidUserStatusException('Invalid status. Allowed values: active, inactive.');
            }

            $user->updateStatus($status);
        }

        $this->repository->update($user);

        return $user;
    }
}
