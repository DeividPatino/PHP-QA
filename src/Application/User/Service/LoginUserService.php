<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Command\LoginUserCommand;
use App\Application\User\Port\UserRepositoryPort;
use App\Domain\User\Entity\UserModel;
use App\Domain\User\Exception\AuthenticationFailedException;

final class LoginUserService
{
    public function __construct(private readonly UserRepositoryPort $repository)
    {
    }

    public function handle(LoginUserCommand $command): UserModel
    {
        $user = $this->repository->findByEmail($command->email);

        if ($user === null || !$user->password()->matches($command->password)) {
            throw new AuthenticationFailedException('Invalid credentials.');
        }

        return $user;
    }
}
