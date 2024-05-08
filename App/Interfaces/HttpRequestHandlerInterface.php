<?php

declare(strict_types=1);

namespace App\Interfaces;

interface HttpRequestHandlerInterface
{
    public function getHttpMethod(): string;

    public function getHttpUri(): string;

    public function getRequestBody(): ?string;
}
