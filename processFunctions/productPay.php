<?php

function payProduct(array $products): void
{
    $requestData = readProducts('php://input');

    validateRequestData(
        [
            ID => 'int',
            CATEGORY => 'string',
            PAYMENT_AMOUNT => 'string'
        ],
        $requestData
    );

    $id = $requestData[ID];
    $category = $requestData[CATEGORY];

    $product = extractProduct($products, $category, $id);

    if ($product === null) {
        sendResponse(toJson(['error' => 'Product not found']), 404);
    }

    if ($product[PRICE] > $requestData[PAYMENT_AMOUNT]) {
        sendResponse(toJson(['error' => 'Insufficient pay amount']), 404);
    }

    $change = $requestData[PAYMENT_AMOUNT] - $product[PRICE];

    $receipt = printReceipt(
        $product[NAME],
        $product[PRICE],
        $requestData[PAYMENT_AMOUNT],
        $change
    );

    sendResponse(toJson($receipt), 200);
}