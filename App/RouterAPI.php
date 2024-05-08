<?php

declare(strict_types=1);

namespace App;

use App\Abstracts\AbstractRouterEngine;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;

class RouterAPI extends AbstractRouterEngine
{
    public function routes(): void
    {
        $this->get('/products-all/', [ProductController::class, 'index']);

        $this->get('/product/', [ProductController::class, 'show']);

        $this->post('/product/', [ProductController::class, 'create']);

        $this->put('/product/', [ProductController::class, 'update']);

        $this->patch('/product/', [ProductController::class, 'patch']);

        $this->delete('/product/', [ProductController::class, 'delete']);

        $this->get('/category-all/', [CategoryController::class, 'index']);

        $this->get('/category/', [CategoryController::class, 'show']);

        $this->post('/category/', [CategoryController::class, 'create']);

        $this->put('/category/', [CategoryController::class, 'update']);

        $this->delete('/category/', [CategoryController::class, 'delete']);

        $this->post('/product-pay/', [ProductController::class, 'pay']);
    }
}
