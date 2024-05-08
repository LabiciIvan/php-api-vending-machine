<?php

declare(strict_types=1);

namespace App;

use App\Services\Json;
use App\Abstracts\AbstractHttpRequestHandler;

class HttpRequest extends AbstractHttpRequestHandler
{
    private ?string $endpoint;

    private ?array $parameters;

    public function __construct()
    {
        $this->parseHttpUri();
    }

    protected function parseHttpUri(): void
    {
        $uri = parse_url($this->getHttpUri());

        $this->endpoint = $uri['path'] ?? null;

        if (isset($uri['query'])) {
            parse_str($uri['query'], $this->parameters);
        }
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function getData(): ?array
    {
        return Json::fromJson($this->getRequestBody());
    }
}
