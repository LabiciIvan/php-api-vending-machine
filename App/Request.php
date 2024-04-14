<?php

declare(strict_types=1);

namespace App;

use App\Services\Log;
use App\Services\Json;
use App\Abstracts\AbstractRequest;

class Request extends AbstractRequest
{
    private ?string $method;

    private ?string $requestURI;

    private ?string $endpoint;

    private ?array $params; 

    private ?string $requestBody;

    public function __construct()
    {
        $this->run();
    }

    protected function accessMethodAndURI(): void
    {
        $this->method = $this->getRequestMethod();

        $this->requestURI = $this->getRequestURI();
    }

    protected function processElementsURI(): void
    {
        $parsedURI = $this->parseURI($this->requestURI);

        $this->endpoint = isset($parsedURI['path']) ? $parsedURI['path'] : null;

        $this->params = isset($parsedURI['query']) ? $this->readParameters($parsedURI['query']) : null;
    }

    protected function processRequestBody(): void
    {
        $this->requestBody = $this->readRequest('php://input');
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function getParameter(): ?array
    {
        return $this->params;
    }

    public function getData(): ?array
    {
        return Json::fromJson($this->requestBody);
    }
}