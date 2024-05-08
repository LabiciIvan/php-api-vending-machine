<?php

declare(strict_types=1);

namespace App;

use App\HttpRequest;
use App\RouterAPI;

class Kernel
{
    public HttpRequest $request;

    public RouterAPI $router;

    public function __construct(HttpRequest $request, RouterAPI $router)
    {
        $this->request = $request;

        $this->router = $router;
    }
}
