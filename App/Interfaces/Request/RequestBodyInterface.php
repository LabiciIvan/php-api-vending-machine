<?php

declare(strict_types=1);

namespace App\Interfaces\Request;

interface RequestBodyInterface
{
    public function readRequest(string $path): ?string;
}