<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Interfaces\Request\RequestBodyInterface;
use App\Interfaces\Request\RequestParametersInterface;

abstract class AbstractRequest implements RequestBodyInterface, RequestParametersInterface
{
    public function readRequest(string $path): ?string
    {
        $requestData = @file_get_contents($path);

        if ($requestData === false) {
            return null;
        }

        return $requestData;
    }

    public function readParameters(string $query): array
    {
        $params = [];

        parse_str($query, $params);

        return $params;
    }

    public function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getRequestURI(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/test/?suite=A&id=1';
    }

    /**
     * @return array|string|int|false|null
     */
    public function parseURI(string $url)
    {
        return parse_url($url);
    }

    public function run(): void
    {
        $this->accessMethodAndURI();
        $this->processElementsURI();
        $this->processRequestBody();
    }

    abstract protected function accessMethodAndURI(): void;

    abstract protected function processElementsURI(): void;

    abstract protected function processRequestBody(): void;

}