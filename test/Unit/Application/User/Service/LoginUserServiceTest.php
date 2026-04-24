<?php

declare(strict_types=1);

namespace Tests\Unit\Application\User\Service;

use App\Application\User\Command\LoginUserCommand;
use App\Application\User\Port\UserRepositoryPort;
use App\Application\User\Service\LoginUserService;
use App\Domain\User\Entity\UserModel;
use App\Domain\User\Enum\UserRole;
use App\Domain\User\Enum\UserStatus;
use App\Domain\User\Exception\AuthenticationFailedException;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\ValueObject\UserName;
use App\Domain\User\ValueObject\UserPassword;
use PHPUnit\Framework\TestCase;

final class LoginUserServiceTest extends TestCase
{
    public function test_it_logs_user_in_with_valid_credentials(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $service = new LoginUserService($repository);

        $user = UserModel::create(
            new UserId('u-200'),
            new UserName('Ada Lovelace'),
            new UserEmail('ada@example.com'),
            UserPassword::fromPlain('StrongPass1'),
            UserRole::USER,
            UserStatus::ACTIVE
        );

        $repository->expects($this->once())
            ->method('findByEmail')
            ->with('ada@example.com')
            ->willReturn($user);

        $logged = $service->handle(new LoginUserCommand('ada@example.com', 'StrongPass1'));

        $this->assertSame('u-200', $logged->id()->value());
    }

    public function test_it_throws_exception_when_user_is_not_found(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $service = new LoginUserService($repository);

        $repository->expects($this->once())
            ->method('findByEmail')
            ->with('missing@example.com')
            ->willReturn(null);

        $this->expectException(AuthenticationFailedException::class);

        $service->handle(new LoginUserCommand('missing@example.com', 'StrongPass1'));
    }

    public function test_it_throws_exception_when_password_is_invalid(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $service = new LoginUserService($repository);

        $user = UserModel::create(
            new UserId('u-201'),
            new UserName('Alan Turing'),
            new UserEmail('alan@example.com'),
            UserPassword::fromPlain('StrongPass1'),
            UserRole::USER,
            UserStatus::ACTIVE
        );

        $repository->expects($this->once())
            ->method('findByEmail')
            ->with('alan@example.com')
            ->willReturn($user);

        $this->expectException(AuthenticationFailedException::class);

        $service->handle(new LoginUserCommand('alan@example.com', 'WrongPass1'));
    }
}
