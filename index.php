<?php

declare(strict_types=1);

include 'processes.php';

$endpoint = '';
$params = [];
$method = '';

processURL($endpoint, $params, $method);

$products = readProducts('products.json');

switch ($endpoint) {
    case 'product':
        processProduct($products, $params, $method);
        break;
    case 'products-all':
        processProductsAll($products, $params, $method);
        break;
    case 'product-pay':
        processProductPay($products, $method);
        break;
    default:
        sendResponse(toJson(['error' => '404 Not Found']), 404);
        break;
}
