<?php

declare(strict_types=1);

namespace App;

use App\Request;
use App\Router;

class Kernel
{
    public Request $request;

    public Router $router;

    public function __construct()
    {
        $this->request = new Request();

        $this->router = new Router();
    }

    public function getRoutes(): array
    {
        return $this->router->getRoutes();
    }

}