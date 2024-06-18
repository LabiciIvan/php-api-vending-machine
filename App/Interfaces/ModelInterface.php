<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ModelInterface
{
    public function one(int $id): ?array;

    public function all(): ?array;

    public function create(array $data): array;

    public function update(array $updateData, bool $patch = false): array;

    public function delete(int $id): array;

    public function save(string $path, array $data, string $entity): bool;
}