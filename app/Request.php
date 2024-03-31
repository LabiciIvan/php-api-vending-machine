<?php

namespace App;

use App\VM;

class Request {

    private ?string $endpoint = null;

    private ?string $method = null;

    private ?array $params = null;

    private ?array $requestBody = null;

    public function __construct()
    {
        $request = VM::processURL();

        $this->endpoint = $request['endpoint'];
        $this->method = strtolower($request['method']);
        $this->params = $request['params'];

        $this->requestBody = VM::readProducts('php://input');
    }

    protected function method(): string
    {
        return $this->method;
    }

    protected function endpoint(): string
    {
        return $this->endpoint;
    }

    protected function params(): ?array
    {
        return $this->params;
    }

    protected function requestBody(): ?array
    {
        return $this->requestBody;
    }
}