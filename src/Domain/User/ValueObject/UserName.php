<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidUserNameException;

final class UserName
{
    public function __construct(private readonly string $value)
    {
        $clean = trim($value);

        if ($clean === '' || mb_strlen($clean) < 2 || mb_strlen($clean) > 100) {
            throw new InvalidUserNameException('User name must contain between 2 and 100 characters.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
