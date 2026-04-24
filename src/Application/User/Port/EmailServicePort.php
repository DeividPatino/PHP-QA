<?php

declare(strict_types=1);

namespace App\Application\User\Port;

use App\Domain\User\Entity\UserModel;

interface EmailServicePort
{
    public function sendWelcomeEmail(UserModel $user): void;
}
