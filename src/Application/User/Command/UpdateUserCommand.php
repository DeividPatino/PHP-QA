<?php

declare(strict_types=1);

namespace App\Application\User\Command;

final class UpdateUserCommand
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $role = null,
        public readonly ?string $status = null
    ) {
    }
}
