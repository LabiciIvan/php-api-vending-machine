<?php

declare(strict_types=1);

namespace App\Services;

class Response
{
    public static function send(string $message, int $statusCode): void
    {
        http_response_code($statusCode);

        echo $message;

        exit;
    }
}
