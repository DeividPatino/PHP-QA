<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Repository;

use App\Application\User\Port\UserRepositoryPort;
use App\Domain\User\Entity\UserModel;
use App\Infrastructure\User\Mapper\UserPdoMapper;
use PDO;

final class SqliteUserRepository implements UserRepositoryPort
{
    public function __construct(
        private readonly PDO $connection,
        private readonly UserPdoMapper $mapper
    ) {
    }

    public function save(UserModel $user): void
    {
        $row = $this->mapper->toRow($user);

        $statement = $this->connection->prepare(
            'INSERT INTO users (id, name, email, password, role, status) VALUES (:id, :name, :email, :password, :role, :status)'
        );

        $statement->execute($row);
    }

    public function update(UserModel $user): void
    {
        $row = $this->mapper->toRow($user);

        $statement = $this->connection->prepare(
            "UPDATE users SET 
                name = :name, 
                email = :email, 
                password = :password, 
                role = :role, 
                status = :status, 
                updated_at = (datetime('now', 'localtime'))
             WHERE id = :id"
        );

        $statement->execute($row);
    }

    public function deleteById(string $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function findById(string $id): ?UserModel
    {
        $statement = $this->connection->prepare('SELECT id, name, email, password, role, status FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapper->toDomain($row);
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $statement = $this->connection->prepare('SELECT id, name, email, password, role, status FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $rows = $statement->fetchAll();

        return array_map(fn (array $row): UserModel => $this->mapper->toDomain($row), $rows);
    }

    public function findByEmail(string $email): ?UserModel
    {
        $statement = $this->connection->prepare('SELECT id, name, email, password, role, status FROM users WHERE email = :email');
        $statement->execute(['email' => $email]);

        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapper->toDomain($row);
    }

    public function existsByEmail(string $email): bool
    {
        $statement = $this->connection->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);

        return $statement->fetchColumn() !== false;
    }
}
