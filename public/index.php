<?php

declare(strict_types=1);

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Command\DeleteUserCommand;
use App\Application\User\Command\LoginUserCommand;
use App\Application\User\Command\UpdateUserCommand;
use App\Application\User\Query\GetUserByIdQuery;
use App\Application\User\Query\ListUsersQuery;
use App\Domain\User\Exception\UserDomainException;

$container = require __DIR__ . '/../src/bootstrap.php';

header('Content-Type: application/json');

function inputData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($contentType, 'application/json')) {
        $json = file_get_contents('php://input');
        $decoded = json_decode($json ?: '{}', true);

        return is_array($decoded) ? $decoded : [];
    }

    return $_POST;
}

function jsonResponse(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function generateId(): string
{
    return bin2hex(random_bytes(16));
}

$route = $_GET['route'] ?? 'list-users';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$data = inputData();

try {
    switch ($route) {
        case 'create-user':
            if ($method !== 'POST') {
                jsonResponse(405, ['error' => 'Method not allowed']);
                break;
            }

            $command = new CreateUserCommand(
                $data['id'] ?? generateId(),
                $data['name'] ?? '',
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['role'] ?? 'user',
                $data['status'] ?? 'active'
            );

            $user = $container['createUserService']->handle($command);
            jsonResponse(201, ['message' => 'User created', 'data' => $container['userResponseMapper']->toArray($user)]);
            break;

        case 'update-user':
            if ($method !== 'POST') {
                jsonResponse(405, ['error' => 'Method not allowed']);
                break;
            }

            $command = new UpdateUserCommand(
                $data['id'] ?? '',
                $data['name'] ?? null,
                $data['email'] ?? null,
                $data['password'] ?? null,
                $data['role'] ?? null,
                $data['status'] ?? null
            );

            $user = $container['updateUserService']->handle($command);
            jsonResponse(200, ['message' => 'User updated', 'data' => $container['userResponseMapper']->toArray($user)]);
            break;

        case 'delete-user':
            if ($method !== 'POST') {
                jsonResponse(405, ['error' => 'Method not allowed']);
                break;
            }

            $command = new DeleteUserCommand($data['id'] ?? '');
            $container['deleteUserService']->handle($command);
            jsonResponse(200, ['message' => 'User deleted']);
            break;

        case 'get-user':
            if ($method !== 'GET') {
                jsonResponse(405, ['error' => 'Method not allowed']);
                break;
            }

            $query = new GetUserByIdQuery($_GET['id'] ?? '');
            $user = $container['getUserByIdService']->handle($query);
            jsonResponse(200, ['data' => $container['userResponseMapper']->toArray($user)]);
            break;

        case 'list-users':
            if ($method !== 'GET') {
                jsonResponse(405, ['error' => 'Method not allowed']);
                break;
            }

            $limit = (int) ($_GET['limit'] ?? 50);
            $offset = (int) ($_GET['offset'] ?? 0);

            $query = new ListUsersQuery($limit, $offset);
            $users = $container['listUsersService']->handle($query);
            jsonResponse(200, ['data' => $container['userResponseMapper']->toArrayList($users)]);
            break;

        case 'login':
            if ($method !== 'POST') {
                jsonResponse(405, ['error' => 'Method not allowed']);
                break;
            }

            $command = new LoginUserCommand(
                $data['email'] ?? '',
                $data['password'] ?? ''
            );

            $user = $container['loginUserService']->handle($command);
            jsonResponse(200, ['message' => 'Login successful', 'data' => $container['userResponseMapper']->toArray($user)]);
            break;

        default:
            jsonResponse(404, ['error' => 'Route not found']);
    }
} catch (UserDomainException $exception) {
    jsonResponse(400, ['error' => $exception->getMessage()]);
} catch (Throwable $exception) {
    jsonResponse(500, ['error' => 'Unexpected error', 'detail' => $exception->getMessage()]);
}
