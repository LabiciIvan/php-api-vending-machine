<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Application;
use App\Kernel;
use App\HttpRequest;
use App\RouterAPI;

$request = new HttpRequest();

$router = new RouterAPI();

$kernel = new Kernel($request, $router);

$app = new Application();

$app->run($kernel);
