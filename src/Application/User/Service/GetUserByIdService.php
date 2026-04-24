<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Port\UserRepositoryPort;
use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\User\Entity\UserModel;
use App\Domain\User\Exception\UserNotFoundException;

final class GetUserByIdService
{
    public function __construct(private readonly UserRepositoryPort $repository)
    {
    }

    public function handle(GetUserByIdQuery $query): UserModel
    {
        $user = $this->repository->findById($query->id);

        if ($user === null) {
            throw new UserNotFoundException('User not found.');
        }

        return $user;
    }
}
