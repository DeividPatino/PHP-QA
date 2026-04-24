<?php

declare(strict_types=1);

namespace Test\Support;

use App\Application\User\Port\UserRepositoryPort;
use App\Domain\User\Entity\UserModel;

final class InMemoryUserRepository implements UserRepositoryPort
{
    /**
     * @var array<string, UserModel>
     */
    private array $items = [];

    public function save(UserModel $user): void
    {
        $this->items[$user->id()->value()] = $user;
    }

    public function update(UserModel $user): void
    {
        $this->items[$user->id()->value()] = $user;
    }

    public function deleteById(string $id): void
    {
        unset($this->items[$id]);
    }

    public function findById(string $id): ?UserModel
    {
        return $this->items[$id] ?? null;
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        return array_slice(array_values($this->items), $offset, $limit);
    }

    public function findByEmail(string $email): ?UserModel
    {
        foreach ($this->items as $item) {
            if ($item->email()->value() === $email) {
                return $item;
            }
        }

        return null;
    }

    public function existsByEmail(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }
}
