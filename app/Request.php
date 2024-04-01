<?php

namespace App;

use App\VM;

class Request {

    public ?string $endpoint;

    public ?string $method;

    public ?array $params; 

    public ?array $body;

    public function __construct()
    {
        $request = VM::processURL();

        $this->endpoint = $request['endpoint'];
        $this->method = strtolower($request['method']);
        $this->params = $request['params'];

        $this->body = VM::readProducts('php://input');
    }
}