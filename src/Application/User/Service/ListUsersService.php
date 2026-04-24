<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\User\Port\UserRepositoryPort;
use App\Application\User\Query\ListUsersQuery;

final class ListUsersService
{
    public function __construct(private readonly UserRepositoryPort $repository)
    {
    }

    public function handle(ListUsersQuery $query): array
    {
        return $this->repository->findAll($query->limit, $query->offset);
    }
}
