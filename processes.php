<?php

declare(strict_types=1);

include 'functions.php';

include __DIR__ . '/processFunctions/product.php';

include __DIR__ . '/processFunctions/productsAll.php';

include __DIR__ . '/processFunctions/productPay.php';

const ID = 'id';
const CATEGORY = 'category';
const NAME = 'name';
const PRICE = 'price';
const QUANTITY = 'quantity';
const PAYMENT_AMOUNT = 'paymentAmount';

function processProduct(array $products, array $params, string $method): void
{
    switch ($method) {
        case 'GET':
            getProduct($products, $params);
            break;
        case 'POST':
            postProduct($products);
            break;
        case 'PUT':
            putProduct($products);
            break;
        case 'PATCH':
            patchProduct($products);
            break;
        case 'DELETE':
            deleteProduct($products, $params);
            break;
        default:
            sendResponse(toJson(['error' => 'Method not allowed']), 405);
            break;
    }
}

function processProductsAll(array $products, array $params, string $method): void
{
    switch ($method) {
        case 'GET':
            getProducts($products, $params);
            break;
        case 'POST':
            postProducts($params);
            break;
        default:
            sendResponse(toJson(['error' => 'Method not allowed']), 405);
            break;
    }
}

function processProductPay(array $products, string $method): void
{
    switch ($method) {
        case 'POST':
            payProduct($products);
            break;
        default:
            sendResponse(toJson(['error' => 'Method not allowed']), 405);
            break;
    }
}