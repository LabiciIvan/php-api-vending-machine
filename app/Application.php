<?php

namespace App;

use App\Router;
use App\ServiceProvider;

class Application extends Request {

    public Router $router;

    public ServiceProvider $provider;

    private array $routes;

    public function __construct()
    {
        parent::__construct();

        $this->router   = new Router;
        $this->provider = new ServiceProvider();
    }

    public function run()
    {
        $this->routes = $this->router->resolve();

        $route = $this->routes[$this->method][$this->endpoint] ?? null;

        if ($route) {
            if (is_array($route)) {

                $className = $route[0];
                $classMethod = $route[1];
                $classInstance = new $className();

                $dependencyExists = $this->provider->exists($className, $classMethod);

                if ($dependencyExists) {
                    $dependencyInstance = $this->provider->inject($className, $classMethod);
                    $classInstance->$classMethod($dependencyInstance);
                }

                $classInstance->$classMethod();

            } elseif (is_callable($route)) {
                $route();
            }
        }

        VM::sendResponse(VM::toJson(['error' => 'Route not supported']), 500);
    }
}