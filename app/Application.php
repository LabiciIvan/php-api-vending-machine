<?php

namespace App;

use App\Router;
use App\Request;

class Application extends Request {

    public Router $router;

    private array $routes;

    public function __construct()
    {
        parent::__construct();

        $this->router = new Router;
    }

    public function run()
    {
        $this->routes = $this->router->resolve();

        $route = $this->routes[$this->method()][$this->endpoint()] ?? null;

        if ($route) {
            if (is_array($route)) {

                $class = new $route[0]($this->params(), $this->requestBody());
                $method = $route[1];

                $class->$method();

            } elseif (is_callable($route)) {
                $route();
            }
        }
    }
}