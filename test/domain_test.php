<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Domain/User/Exception/UserDomainException.php';
require_once __DIR__ . '/../src/Domain/User/Exception/InvalidUserEmailException.php';
require_once __DIR__ . '/../src/Domain/User/ValueObject/UserEmail.php';

use App\Domain\User\ValueObject\UserEmail;

try {
    $email = new UserEmail('test@test.com');
    echo "Email valido OK: " . $email->value() . PHP_EOL;
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}