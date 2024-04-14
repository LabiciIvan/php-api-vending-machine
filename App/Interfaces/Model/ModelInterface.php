<?php

declare(strict_types=1);

namespace App\Interfaces\Model;

interface ModelInterface
{
    public function save(array $data): bool;

    public function getProducts(): ?array;
}