<?php

declare(strict_types=1);

namespace App\Services;

class Log
{
    public static function errors(string $customMessage, string $errorMessage, int $line): void
    {
        error_log(
            date('Y-m-d H:i:s') . " - Line: {$line} = {$customMessage} {$errorMessage} " . PHP_EOL,
            3,
            'errors.txt'
        );
    }
}
