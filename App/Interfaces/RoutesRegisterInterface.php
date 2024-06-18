<?php

namespace App\Interfaces;

interface RoutesRegisterInterface
{
    public function get(string $path, callable|array $callbackOrArray): void;

    public function post(string $path, callable|array $callbackOrArray): void;

    public function put(string $path, callable|array $callbackOrArray): void;

    public function patch(string $path, callable|array $callbackOrArray): void;

    public function delete(string $path, callable|array $callbackOrArray): void;
}
