<?php

declare(strict_types=1);

namespace App\Interfaces;

interface FileInterface
{
    public static function readFile(string $path): ?string;

    public static function writeFile(string $path, string $file): bool;
}
