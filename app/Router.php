<?php

namespace App;

class Router {

    protected array $routes = [];

    public function get(string $path,callable|array $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, callable|array $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }

    public function put(string $path, callable|array $callback): void
    {
        $this->routes['put'][$path] = $callback;
    }

    public function patch(string $path, callable|array $callback): void
    {
        $this->routes['patch'][$path] = $callback;
    }

    public function delete(string $path, callable|array $callback): void
    {
        $this->routes['delete'][$path] = $callback;
    }

    public function resolve(): array
    {
        return $this->routes;
    }
}