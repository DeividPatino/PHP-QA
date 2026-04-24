<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
