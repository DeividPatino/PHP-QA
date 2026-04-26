<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User\ValueObject;

use App\Domain\User\Exception\InvalidUserEmailException;
use App\Domain\User\Exception\InvalidUserIdException;
use App\Domain\User\Exception\InvalidUserNameException;
use App\Domain\User\Exception\InvalidUserPasswordException;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;
use PHPUnit\Framework\TestCase;

final class UserValueObjectsTest extends TestCase
{
    public function test_user_email_accepts_valid_value(): void
    {
        $email = new UserEmail('valid@example.com');

        $this->assertSame('valid@example.com', $email->value());
    }

    public function test_user_email_rejects_invalid_value(): void
    {
        $this->expectException(InvalidUserEmailException::class);

        new UserEmail('invalid-email');
    }

    public function test_user_name_accepts_valid_value(): void
    {
        $name = new UserName('Ada Lovelace');

        $this->assertSame('Ada Lovelace', $name->value());
    }

    public function test_user_name_rejects_too_short_value(): void
    {
        $this->expectException(InvalidUserNameException::class);

        new UserName('A');
    }

    public function test_user_id_accepts_valid_value(): void
    {
        $id = new UserId('u-300');

        $this->assertSame('u-300', $id->value());
    }

    public function test_user_id_rejects_empty_value(): void
    {
        $this->expectException(InvalidUserIdException::class);

        new UserId('   ');
    }

    public function test_user_password_accepts_valid_plain_text(): void
    {
        $password = UserPassword::fromPlain('StrongPass1');

        $this->assertTrue($password->matches('StrongPass1'));
    }

    public function test_user_password_rejects_short_plain_text(): void
    {
        $this->expectException(InvalidUserPasswordException::class);

        UserPassword::fromPlain('Abc123');
    }

    public function test_user_password_accepts_plain_text_without_uppercase_or_number(): void
    {
        $password = UserPassword::fromPlain('alllowercase');

        $this->assertTrue($password->matches('alllowercase'));
    }
}
