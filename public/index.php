<?php

declare(strict_types=1);

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Command\DeleteUserCommand;
use App\Application\User\Command\LoginUserCommand;
use App\Application\User\Command\UpdateUserCommand;
use App\Application\User\Query\GetUserByIdQuery;
use App\Application\User\Query\ListUsersQuery;
use App\Domain\User\Exception\UserDomainException;

session_start();

$container = require __DIR__ . '/../src/bootstrap.php';

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
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function generateId(): string
{
    return bin2hex(random_bytes(16));
}

function isJsonRequest(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $format = $_GET['format'] ?? '';

    return str_contains($accept, 'application/json') || $format === 'json';
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

function redirect(string $route, array $params = []): void
{
    $query = http_build_query(array_merge(['route' => $route], $params));
    header('Location: index.php?' . $query);
    exit;
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(): void
{
    if (currentUser() === null) {
        setFlash('error', 'Debes iniciar sesion.');
        redirect('login');
    }
}

function requireAdmin(): void
{
    $user = currentUser();

    if ($user === null || ($user['role'] ?? '') !== 'admin') {
        setFlash('error', 'No tienes permiso para ver esa pagina.');
        redirect('login');
    }
}

function render(string $view, array $params = []): void
{
    $flash = getFlash();
    $currentUser = currentUser();

    extract($params, EXTR_SKIP);
    require __DIR__ . '/views/' . $view . '.php';
}

$route = $_GET['route'] ?? (isJsonRequest() ? 'list-users' : 'login');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$data = inputData();
$isApi = isJsonRequest();

try {
    if ($isApi) {
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

        exit;
    }

    switch ($route) {
        case 'register':
            if ($method === 'POST') {
                $command = new CreateUserCommand(
                    generateId(),
                    $data['name'] ?? '',
                    $data['email'] ?? '',
                    $data['password'] ?? '',
                    $data['role'] ?? 'user',
                    'active'
                );

                $container['createUserService']->handle($command);
                setFlash('success', 'Usuario creado. Ahora puedes iniciar sesion.');
                redirect('login');
            }

            render('register');
            break;

        case 'login':
            if ($method === 'POST') {
                $command = new LoginUserCommand(
                    $data['email'] ?? '',
                    $data['password'] ?? ''
                );

                $user = $container['loginUserService']->handle($command);

                $_SESSION['user'] = [
                    'id' => $user->id()->value(),
                    'name' => $user->name()->value(),
                    'email' => $user->email()->value(),
                    'role' => $user->role()->value,
                ];

                if ($user->role()->value === 'admin') {
                    redirect('users');
                }

                redirect('profile');
            }

            render('login');
            break;

        case 'logout':
            unset($_SESSION['user']);
            setFlash('success', 'Sesion cerrada.');
            redirect('login');
            break;

        case 'users':
            requireAdmin();
            $query = new ListUsersQuery(200, 0);
            $users = $container['listUsersService']->handle($query);
            render('users_list', ['users' => $users]);
            break;

        case 'user-edit':
            requireLogin();
            $current = currentUser();
            $isAdmin = ($current['role'] ?? '') === 'admin';
            $userId = $data['id'] ?? ($_GET['id'] ?? $current['id']);

            if (!$isAdmin && $userId !== $current['id']) {
                setFlash('error', 'No puedes editar ese perfil.');
                redirect('profile');
            }

            if ($method === 'POST') {
                $command = new UpdateUserCommand(
                    $userId,
                    $data['name'] ?? null,
                    $data['email'] ?? null,
                    $data['password'] !== '' ? ($data['password'] ?? null) : null,
                    $isAdmin ? ($data['role'] ?? null) : null,
                    null
                );

                $updated = $container['updateUserService']->handle($command);

                if ($current['id'] === $updated->id()->value()) {
                    $_SESSION['user']['name'] = $updated->name()->value();
                    $_SESSION['user']['email'] = $updated->email()->value();
                    $_SESSION['user']['role'] = $updated->role()->value;
                }

                setFlash('success', 'Usuario actualizado.');
                redirect($isAdmin ? 'users' : 'profile');
            }

            $query = new GetUserByIdQuery($userId);
            $user = $container['getUserByIdService']->handle($query);
            render('user_edit', ['user' => $user, 'isAdmin' => $isAdmin]);
            break;

        case 'user-delete':
            requireAdmin();
            if ($method !== 'POST') {
                setFlash('error', 'Metodo no permitido.');
                redirect('users');
            }

            $command = new DeleteUserCommand($data['id'] ?? '');
            $container['deleteUserService']->handle($command);
            setFlash('success', 'Usuario eliminado.');
            redirect('users');
            break;

        case 'profile':
            requireLogin();
            redirect('user-edit', ['id' => currentUser()['id']]);
            break;

        default:
            redirect('login');
    }
} catch (UserDomainException $exception) {
    if ($isApi) {
        jsonResponse(400, ['error' => $exception->getMessage()]);
        exit;
    }

    $fallbackRoute = 'login';
    if ($route === 'register') {
        $fallbackRoute = 'register';
    }
    if ($route === 'user-edit') {
        $id = $data['id'] ?? ($_GET['id'] ?? '');
        setFlash('error', $exception->getMessage());
        redirect('user-edit', ['id' => $id]);
    }

    setFlash('error', $exception->getMessage());
    redirect($fallbackRoute);
} catch (Throwable $exception) {
    if ($isApi) {
        jsonResponse(500, ['error' => 'Unexpected error', 'detail' => $exception->getMessage()]);
        exit;
    }

    setFlash('error', 'Error inesperado.');
    redirect('login');
}
