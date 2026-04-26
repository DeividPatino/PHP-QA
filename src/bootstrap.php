<?php

declare(strict_types=1);

use App\Application\User\Mapper\UserResponseMapper;
use App\Application\User\Service\CreateUserService;
use App\Application\User\Service\DeleteUserService;
use App\Application\User\Service\GetUserByIdService;
use App\Application\User\Service\ListUsersService;
use App\Application\User\Service\LoginUserService;
use App\Application\User\Service\UpdateUserService;
use App\Infrastructure\Email\EmailService;
use App\Infrastructure\User\Mapper\UserPdoMapper;
use App\Infrastructure\User\Repository\SqliteUserRepository;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

$dbPath = __DIR__ . '/../database/php_qa.sqlite';
$dbExists = file_exists($dbPath);

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!$dbExists) {
    try {
        $schema = file_get_contents(__DIR__ . '/../database/schema.sqlite.sql');
        $pdo->exec($schema);
    } catch (PDOException $e) {
        die('Could not initialize database schema: ' . $e->getMessage());
    }
}

$userRepository = new SqliteUserRepository($pdo, new UserPdoMapper());
$emailService = new EmailService();

return [
    'createUserService' => new CreateUserService($userRepository, $emailService),
    'updateUserService' => new UpdateUserService($userRepository),
    'deleteUserService' => new DeleteUserService($userRepository),
    'getUserByIdService' => new GetUserByIdService($userRepository),
    'listUsersService' => new ListUsersService($userRepository),
    'loginUserService' => new LoginUserService($userRepository),
    'userResponseMapper' => new UserResponseMapper(),
];
