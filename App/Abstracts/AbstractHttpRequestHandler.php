<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Interfaces\HttpRequestHandlerInterface;

abstract class AbstractHttpRequestHandler implements HttpRequestHandlerInterface
{
    public function getHttpMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getHttpUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getRequestBody(): ?string
    {
        $requestBody = file_get_contents('php://input');

        if ($requestBody === false) {
            return null;
        }

        return $requestBody;
    }
}
