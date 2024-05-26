<?php

declare(strict_types=1);

namespace App;

use App\Kernel;
use App\Services\Json;
use App\ServiceProvider;
use App\Services\Response;

class Application
{
    public function run(Kernel $kernel)
    {
        $kernel->router->setRoutes();

        $routes = $kernel->router->getRoutes();

        $method = $kernel->request->getHttpMethod();

        $endpoint = $kernel->request->getEndpoint();

        $route = $routes[$method][$endpoint] ?? null;

        if ($route) {
            if (is_array($route)) {

                $className = $route[0];
                $classMethod = $route[1];
                $classInstance = new $className();

                // Check if this class method has any dependencies.
                $provider = new ServiceProvider($className, $classMethod);

                $dependencies = $provider->has();

                if ($dependencies) {
                    $instances = array_map(function($class) {
                        return new $class();
                    }, $dependencies);

                    $classInstance->$classMethod(...$instances);
                }

                $classInstance->$classMethod();

            } elseif (is_callable($route)) {
                $route();
            }
        }

        Response::send(Json::toJson(['error' => 'Route not supported']), 500);
    }
}
