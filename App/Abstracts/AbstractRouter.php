<?php

declare(strict_types=1);

namespace App\Abstracts;

abstract class AbstractRouter
{
    private array $routes = [];

    protected function get(string $path,callable|array $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    protected function post(string $path, callable|array $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    protected function put(string $path, callable|array $callback): void
    {
        $this->routes['PUT'][$path] = $callback;
    }

    protected function patch(string $path, callable|array $callback): void
    {
        $this->routes['PATCH'][$path] = $callback;
    }

    protected function delete(string $path, callable|array $callback): void
    {
        $this->routes['DELETE'][$path] = $callback;
    }

    protected function initialiseRoutes(): void
    {
        $this->routes();
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    abstract public function routes();
}