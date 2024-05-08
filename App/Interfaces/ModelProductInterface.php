<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ModelProductInterface
{
    public function one(string $category, int $id): ?array;

    public function all(): ?array;

    public function create(array $data): int;

    public function update(array $toUpdate, array $data = null, bool $patch = false): void;

    public function delete(?string $category = null, ?int $id = null): void;

    public function save(string $path): bool;
}
