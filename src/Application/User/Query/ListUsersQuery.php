<?php

declare(strict_types=1);

namespace App\Application\User\Query;

final class ListUsersQuery
{
    public function __construct(
        public readonly int $limit = 50,
        public readonly int $offset = 0
    ) {
    }
}
