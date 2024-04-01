<?php

namespace App;

class ServiceProvider {

    private array $dependencies = [
        'App\Controllers\CategoryController' => [
            'show'      => 'App\Requests\CategoryRequest',
            'update'    => 'App\Requests\CategoryRequest',
            'delete'    => 'App\Requests\CategoryRequest'
        ],
        'App\Controllers\ProductController' => [
            'show'      => 'App\Requests\ProductRequest',
            'create'    => 'App\Requests\ProductRequest',
            'update'    => 'App\Requests\ProductRequest',
            'patch'     => 'App\Requests\ProductRequest',
            'delete'    => 'App\Requests\ProductRequest',
            'pay'       => 'App\Requests\ProductRequest',
        ]
    ];

    public function exists(string $controllerName, string $controllerMethod): bool
    {
        return isset($this->dependencies[$controllerName][$controllerMethod]);
    }

    public function inject(string $controllerName, string $controllerMethod): mixed
    {
        $class = $this->dependencies[$controllerName][$controllerMethod];
        return new $class();
    }
}