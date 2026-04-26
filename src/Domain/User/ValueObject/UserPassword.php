<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidUserPasswordException;

final class UserPassword
{
    private function __construct(private readonly string $hashedValue)
    {
        if (trim($this->hashedValue) === '') {
            throw new InvalidUserPasswordException('Password hash cannot be empty.');
        }
    }

    public static function fromPlain(string $plainPassword): self
    {
        if (mb_strlen($plainPassword) < 8) {
            throw new InvalidUserPasswordException('Password must have at least 8 characters.');
        }

        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);

        if ($hash === false) {
            throw new InvalidUserPasswordException('Could not hash password.');
        }

        return new self($hash);
    }

    public static function fromHashed(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function value(): string
    {
        return $this->hashedValue;
    }

    public function matches(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hashedValue);
    }
}
