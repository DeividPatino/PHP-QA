<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Support/InMemoryEmailService.php';
require_once __DIR__ . '/Support/InMemoryUserRepository.php';

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Command\DeleteUserCommand;
use App\Application\User\Command\LoginUserCommand;
use App\Application\User\Command\UpdateUserCommand;
use App\Application\User\Query\GetUserByIdQuery;
use App\Application\User\Query\ListUsersQuery;
use App\Application\User\Service\CreateUserService;
use App\Application\User\Service\DeleteUserService;
use App\Application\User\Service\GetUserByIdService;
use App\Application\User\Service\ListUsersService;
use App\Application\User\Service\LoginUserService;
use App\Application\User\Service\UpdateUserService;
use App\Domain\User\Exception\UserNotFoundException;
use Test\Support\InMemoryEmailService;
use Test\Support\InMemoryUserRepository;

$repo = new InMemoryUserRepository();
$email = new InMemoryEmailService();
$create = new CreateUserService($repo, $email);
$update = new UpdateUserService($repo);
$delete = new DeleteUserService($repo);
$getById = new GetUserByIdService($repo);
$list = new ListUsersService($repo);
$login = new LoginUserService($repo);

try {
    $create->handle(new CreateUserCommand(
        'u-001',
        'Ada Lovelace',
        'ada@example.com',
        'StrongPass1',
        'user',
        'active'
    ));

    $users = $list->handle(new ListUsersQuery());
    echo '1) Usuarios tras create: ' . count($users) . PHP_EOL;
    echo '1.1) Emails enviados tras create: ' . $email->totalSent() . PHP_EOL;

    $updated = $update->handle(new UpdateUserCommand(
        'u-001',
        'Ada L.',
        'ada.new@example.com',
        null,
        'admin',
        'active'
    ));
    echo '2) Usuario actualizado: ' . $updated->name()->value() . ' - ' . $updated->role()->value . PHP_EOL;

    $fetched = $getById->handle(new GetUserByIdQuery('u-001'));
    echo '3) Usuario por ID: ' . $fetched->email()->value() . PHP_EOL;

    $logged = $login->handle(new LoginUserCommand('ada.new@example.com', 'StrongPass1'));
    echo '4) Login OK para: ' . $logged->id()->value() . PHP_EOL;

    $delete->handle(new DeleteUserCommand('u-001'));
    $afterDelete = $list->handle(new ListUsersQuery());
    echo '5) Usuarios tras delete: ' . count($afterDelete) . PHP_EOL;

    try {
        $getById->handle(new GetUserByIdQuery('u-001'));
        echo 'ERROR: se esperaba UserNotFoundException' . PHP_EOL;
    } catch (UserNotFoundException) {
        echo '6) Verificacion delete OK (usuario no existe)' . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'Fallo en flujo: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo 'Flujo completo OK' . PHP_EOL;
