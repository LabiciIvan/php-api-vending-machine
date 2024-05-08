<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\FileInterface;

class File implements FileInterface
{
    public static function readFile(string $path): ?string
    {
        $fileContent = @file_get_contents($path);

        if ($fileContent === false) {
            return null;
        }

        return $fileContent;
    }

    public static function writeFile(string $path, string $file): bool
    {
        $isContentSaved = file_put_contents($path, $file);

        return is_int($isContentSaved);
    }
}