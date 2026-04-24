<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Command\DeleteUserCommand;
use App\Application\User\Port\UserRepositoryPort;
use App\Domain\User\Exception\UserNotFoundException;

final class DeleteUserService
{
    public function __construct(private readonly UserRepositoryPort $repository)
    {
    }

    public function handle(DeleteUserCommand $command): void
    {
        $user = $this->repository->findById($command->id);

        if ($user === null) {
            throw new UserNotFoundException('User not found.');
        }

        $this->repository->deleteById($command->id);
    }
}
