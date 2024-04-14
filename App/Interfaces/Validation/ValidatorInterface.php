<?php

declare(strict_types=1);

namespace App\Interfaces\Validation;

interface ValidatorInterface
{
    public function isInt(string $field, mixed $data): ?string;

    public function isString(string $field, mixed $data): ?string;

    public function isArray(string $field, mixed $data): ?string;

    public function isRequired(string $field, array $data): ?string;

    public function getErrors(): array;

}