<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Port\EmailServicePort;
use App\Application\User\Port\UserRepositoryPort;
use App\Domain\User\Entity\UserModel;
use App\Domain\User\Enum\UserRole;
use App\Domain\User\Enum\UserStatus;
use App\Domain\User\Exception\InvalidUserRoleException;
use App\Domain\User\Exception\InvalidUserStatusException;
use App\Domain\User\Exception\UserAlreadyExistsException;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;

final class CreateUserService
{
    public function __construct(
        private readonly UserRepositoryPort $repository,
        private readonly EmailServicePort $emailService
    ) {
    }

    public function handle(CreateUserCommand $command): UserModel
    {
        if ($this->repository->existsByEmail($command->email)) {
            throw new UserAlreadyExistsException('Email is already registered.');
        }

        try {
            $role = UserRole::from($command->role);
        } catch (\ValueError) {
            throw new InvalidUserRoleException('Invalid role. Allowed values: admin, user.');
        }

        try {
            $status = UserStatus::from($command->status);
        } catch (\ValueError) {
            throw new InvalidUserStatusException('Invalid status. Allowed values: active, inactive.');
        }

        $user = UserModel::create(
            new UserId($command->id),
            new UserName($command->name),
            new UserEmail($command->email),
            UserPassword::fromPlain($command->password),
            $role,
            $status
        );

        $this->repository->save($user);
        $this->emailService->sendWelcomeEmail($user);

        return $user;
    }
}
