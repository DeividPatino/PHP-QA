<?php

declare(strict_types=1);

namespace App\Application\User\Command;

final class DeleteUserCommand
{
    public function __construct(public readonly string $id)
    {
    }
}
