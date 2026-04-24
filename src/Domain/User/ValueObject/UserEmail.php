<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidUserEmailException;

final class UserEmail
{
    public function __construct(private readonly string $value)
    {
        $clean = trim($value);

        if (!filter_var($clean, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidUserEmailException('Invalid email format.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
