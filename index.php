<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/constants.php';

use App\Application;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;

$app = new Application();

$app->router->get('products-all', [ProductController::class, 'index']);

$app->router->get('product', [ProductController::class, 'show']);

$app->router->post('product', [ProductController::class, 'upload']);

$app->router->put('product', [ProductController::class, 'update']);

$app->router->patch('product', [ProductController::class, 'patch']);

$app->router->delete('product', [ProductController::class, 'delete']);

$app->router->post('product-pay', [ProductController::class, 'pay']);

$app->router->get('category', [CategoryController::class, 'index']);

$app->router->post('category', [CategoryController::class, 'upload']);

$app->run();
