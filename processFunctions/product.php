<?php

declare(strict_types=1);

function getProduct(array $products, array $params): void
{
    validateURLParameters($params, 2, [CATEGORY, ID]);

    $category = $params[CATEGORY];
    $id = (int)$params[ID];

    $product = extractProduct($products, $category, $id);

    if ($product === null) {
        sendResponse(toJson(['error' => 'Product not found']), 404);
    }

    sendResponse(toJson($product), 200);
}

function postProduct(array $products): void
{
    $requestData = readProducts('php://input');

    $validationError = validateRequestData(
        [
            CATEGORY => 'string',
            PRICE => 'string',
            NAME => 'string',
            QUANTITY => 'int'
        ],
        $requestData
    );

    if ($validationError) {
        sendResponse(toJson(['errors' => $validationError]), 400);
    }

    addToList($products, $requestData, null, true);

    $isSaved = saveProducts($products, 'products.json');

    if (!$isSaved) {
        sendResponse((toJson(['error' => 'Error uploading new product'])), 500);
    }

    sendResponse((toJson(['data' => 'Product uploaded.'])), 200);
}

function putProduct(array $products): void
{
    $requestData = readProducts('php://input');

    $validationError = validateRequestData(
        [
            ID => 'int',
            CATEGORY => 'string',
            NAME => 'string',
            PRICE => 'string',
            QUANTITY => 'int'

        ],
        $requestData
    );

    if ($validationError) {
        sendResponse(toJson(['errors' => $validationError]), 400);
    }

    $product = extractProduct($products, $requestData[CATEGORY], $requestData[ID]);

    if ($product === null) {
        sendResponse(toJson(['error' => 'Product not found']), 404);
    }

    addToList($products, $requestData, $product);

    $isSaved = saveProducts($products, 'products.json');

    if (!$isSaved) {
        sendResponse((toJson(['error' => 'Error uploading new product'])), 500);
    }

    sendResponse(toJson(['data' => 'Product updated']), 200);
}

function patchProduct(array $products): void
{
    $requestData = readProducts('php://input');

    $validationError = validateRequestData([ID => 'int', CATEGORY => 'string'], $requestData);

    if ($validationError) {
        sendResponse(toJson(['errors' => $validationError]), 400);
    }

    $allowedToPatch = [NAME => 'string', PRICE => 'string', QUANTITY => 'int'];

    $typeErrors = validateType($allowedToPatch, $requestData, true);

    if ($typeErrors) {
        sendResponse(toJson(['error' => $typeErrors]), 400);
    }

    $product = extractProduct($products, $requestData[CATEGORY], $requestData[ID]);

    if ($product === null) {
        sendResponse(toJson(['error' => 'Product not found']), 404);
    }

    addToList($products, $requestData, $product);

    $isSaved = saveProducts($products, 'products.json');

    if (!$isSaved) {
        sendResponse(toJson(['error' => 'Error patch new product']), 400);
    }

    sendResponse(toJson(['data' => 'Product updated']), 200);
}

function deleteProduct(array $products, array $params): void
{
    validateURLParameters($params, 2, [CATEGORY, ID]);

    $category = $params[CATEGORY];
    $id = (int)$params[ID];

    $product = extractProduct($products, $category, $id);

    if ($product === null) {
        sendResponse(toJson(['error' => 'Product not found']), 404);
    }

    foreach($products[$category] as $key => $product) {
        if ($product[ID] === $id) {
            $position = $key;
        }
    }

    unset($products[$category][$position]);

    $isSaved = saveProducts($products, 'products.json');

    if (!$isSaved) {
        sendResponse((toJson(['error' => 'Error deleting product'])), 500);
    }

    sendResponse(toJson(['data' => 'Product deleted']), 200);
}