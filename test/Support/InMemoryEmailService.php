<?php

declare(strict_types=1);

namespace Test\Support;

use App\Application\User\Port\EmailServicePort;
use App\Domain\User\Entity\UserModel;

final class InMemoryEmailService implements EmailServicePort
{
    /**
     * @var array<int, array<string, string>>
     */
    private array $sent = [];

    public function sendWelcomeEmail(UserModel $user): void
    {
        $this->sent[] = [
            'to' => $user->email()->value(),
            'name' => $user->name()->value(),
            'subject' => 'Bienvenido a la plataforma',
        ];
    }

    public function totalSent(): int
    {
        return count($this->sent);
    }
}
