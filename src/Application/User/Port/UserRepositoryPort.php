<?php

declare(strict_types=1);

namespace App\Application\User\Port;

use App\Domain\User\Entity\UserModel;

interface UserRepositoryPort
{
    public function save(UserModel $user): void;

    public function update(UserModel $user): void;

    public function deleteById(string $id): void;

    public function findById(string $id): ?UserModel;

    /**
     * @return UserModel[]
     */
    public function findAll(int $limit = 50, int $offset = 0): array;

    public function findByEmail(string $email): ?UserModel;

    public function existsByEmail(string $email): bool;
}
