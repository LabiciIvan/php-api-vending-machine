<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ModelCategoryInterface
{
    public function one(string $category): ?array;

    public function all(): ?array;

    public function create(array $data): void;

    public function update(array $toUpdate): void;

    public function delete(string $category): void;

    public function save(string $path): bool;
}
