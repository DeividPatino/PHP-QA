<?php

declare(strict_types=1);

namespace Tests\Unit\Application\User\Service;

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Port\EmailServicePort;
use App\Application\User\Port\UserRepositoryPort;
use App\Application\User\Service\CreateUserService;
use App\Domain\User\Exception\InvalidUserRoleException;
use App\Domain\User\Exception\InvalidUserStatusException;
use App\Domain\User\Exception\UserAlreadyExistsException;
use PHPUnit\Framework\TestCase;

final class CreateUserServiceTest extends TestCase
{
    public function test_it_creates_user_and_sends_welcome_email(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $emailService = $this->createMock(EmailServicePort::class);

        $repository->expects($this->once())
            ->method('existsByEmail')
            ->with('ada@example.com')
            ->willReturn(false);

        $repository->expects($this->once())
            ->method('save');

        $emailService->expects($this->once())
            ->method('sendWelcomeEmail');

        $service = new CreateUserService($repository, $emailService);

        $result = $service->handle(new CreateUserCommand(
            'u-100',
            'Ada Lovelace',
            'ada@example.com',
            'StrongPass1',
            'user',
            'active'
        ));

        $this->assertSame('u-100', $result->id()->value());
        $this->assertSame('ada@example.com', $result->email()->value());
        $this->assertSame('user', $result->role()->value);
        $this->assertSame('active', $result->status()->value);
    }

    public function test_it_throws_exception_when_email_already_exists(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $emailService = $this->createMock(EmailServicePort::class);

        $repository->expects($this->once())
            ->method('existsByEmail')
            ->with('taken@example.com')
            ->willReturn(true);

        $repository->expects($this->never())->method('save');
        $emailService->expects($this->never())->method('sendWelcomeEmail');

        $service = new CreateUserService($repository, $emailService);

        $this->expectException(UserAlreadyExistsException::class);

        $service->handle(new CreateUserCommand(
            'u-101',
            'Alan Turing',
            'taken@example.com',
            'StrongPass1',
            'user',
            'active'
        ));
    }

    public function test_it_throws_exception_when_role_is_invalid(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $emailService = $this->createMock(EmailServicePort::class);

        $repository->method('existsByEmail')->willReturn(false);
        $repository->expects($this->never())->method('save');
        $emailService->expects($this->never())->method('sendWelcomeEmail');

        $service = new CreateUserService($repository, $emailService);

        $this->expectException(InvalidUserRoleException::class);

        $service->handle(new CreateUserCommand(
            'u-102',
            'Grace Hopper',
            'grace@example.com',
            'StrongPass1',
            'super-admin',
            'active'
        ));
    }

    public function test_it_throws_exception_when_status_is_invalid(): void
    {
        $repository = $this->createMock(UserRepositoryPort::class);
        $emailService = $this->createMock(EmailServicePort::class);

        $repository->method('existsByEmail')->willReturn(false);
        $repository->expects($this->never())->method('save');
        $emailService->expects($this->never())->method('sendWelcomeEmail');

        $service = new CreateUserService($repository, $emailService);

        $this->expectException(InvalidUserStatusException::class);

        $service->handle(new CreateUserCommand(
            'u-103',
            'Edsger Dijkstra',
            'edsger@example.com',
            'StrongPass1',
            'user',
            'pending'
        ));
    }
}
