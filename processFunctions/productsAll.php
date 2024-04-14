<?php

function getProducts(array $products, array $params): void
{
    validateURLParameters($params, 1, [CATEGORY]);

    $category = $params[CATEGORY];

    $products = extractProduct($products, $category);

    if ($products === null) {
        sendResponse(toJson(['error' => 'Products not found']), 404);
    }

    sendResponse(toJson($products), 200);
}

function postProducts($url): void
{
    $requestData = readProducts('php://input');

    if ($requestData === null) {
        sendResponse(toJson(['error' => 'Data sent is not in the right format']), 400);
    }

    $validationError = validateProducts(
        $requestData,
        [
            ID => 'int',
            QUANTITY => 'int',
            NAME => 'string',
            PRICE => 'string'
        ]
    );

    if ($validationError) {
        sendResponse(toJson(['error' => $validationError]), 400);
    }

    $isSaved = saveProducts($requestData, 'products.json');

    if (!$isSaved) {
        sendResponse((toJson(['error' => 'Error uploading new products'])), 500);
    }

    sendResponse(toJson(['data' => 'Products Uploaded']), 200);
}