<?php

declare(strict_types=1);

namespace App\Application\User\Command;

final class LoginUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }
}
