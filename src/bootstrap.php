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
use App\Infrastructure\Persistence\PDO\PDOConnectionFactory;
use App\Infrastructure\User\Mapper\UserPdoMapper;
use App\Infrastructure\User\Repository\PdoUserRepository;

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

$config = [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'dbname' => getenv('DB_NAME') ?: 'php_qa',
    'user' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
];

$pdo = PDOConnectionFactory::create($config);
$userRepository = new PdoUserRepository($pdo, new UserPdoMapper());
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
