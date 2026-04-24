<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidUserIdException;

final class UserId
{
    public function __construct(private readonly string $value)
    {
        if (trim($value) === '') {
            throw new InvalidUserIdException('User ID cannot be empty.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
