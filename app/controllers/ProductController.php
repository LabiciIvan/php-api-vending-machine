<?php

namespace App\Controllers;

use App\Models\Product;
use App\VM;

class ProductController {

    private ?array $params;

    private ?array $requestBody;

    private Product $productModel;

    public function __construct(?array $params, ?array $requestBody)
    {
        $this->params = $params;
        $this->requestBody = $requestBody;
        $this->productModel = new Product();
    }

    public function index(): void
    {
        VM::validateURLParameters($this->params, 1, [CATEGORY]);

        $category = $this->params[CATEGORY];

        $products = $this->productModel->all($category);

        if ($products === null) {
            VM::sendResponse(VM::toJson(['error' => 'Products not found']), 404);
        }

        VM::sendResponse(VM::toJson($products), 200);
    }

    public function show(): void
    {
        VM::validateURLParameters($this->params, 2, [CATEGORY, ID]);

        $category = $this->params[CATEGORY];
        $id = (int)$this->params[ID];

        $product = $this->productModel->one($category, $id);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        VM::sendResponse(VM::toJson($product), 200);
    }

    public function upload()
    {
        $validationError = VM::validateRequestData(
            [
                CATEGORY => 'string',
                PRICE =>    'string',
                NAME =>     'string',
                QUANTITY => 'int'
            ],
            $this->requestBody
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }

        $category = $this->requestBody[CATEGORY];

        $categoryProducts = $this->productModel->all($category);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn/\'t exists.']), 404);
        }

        $createdProduct = $this->productModel->create($this->requestBody);

        $isStored = $this->productModel->storeProduct($createdProduct, $category, __DIR__ . LOCATION_PRODUCT);

        if (!$isStored) {
            VM::sendResponse((VM::toJson(['error' => 'Error uploading new product'])), 500);
        }

        VM::sendResponse((VM::toJson(['data' => 'Product uploaded.'])), 200);
    }

    public function update()
    {
        $validationError = VM::validateRequestData(
            [
                ID =>       'int',
                CATEGORY => 'string',
                NAME =>     'string',
                PRICE =>    'string',
                QUANTITY => 'int'
            ],
            $this->requestBody
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }

        $product = $this->productModel->one($this->requestBody[CATEGORY], $this->requestBody[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isUpdated = $this->productModel->update($product, $this->requestBody, __DIR__ . LOCATION_PRODUCT);

        if (!$isUpdated) {
            VM::sendResponse((VM::toJson(['error' => 'Error updating new product'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Product updated']), 200);
    }

    public function patch(): void
    {
        $validationError = VM::validateRequestData([ID => 'int', CATEGORY => 'string'], $this->requestBody);

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }

        $allowedToPatch = [NAME => 'string', PRICE => 'string', QUANTITY => 'int'];

        $typeErrors = VM::validateType($allowedToPatch, $this->requestBody, true);

        if ($typeErrors) {
            VM::sendResponse(VM::toJson(['error' => $typeErrors]), 400);
        }

        $product = $this->productModel->one($this->requestBody[CATEGORY], $this->requestBody[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isPatched = $this->productModel->update($product, $this->requestBody, __DIR__ . LOCATION_PRODUCT, true);

        if (!$isPatched) {
            VM::sendResponse(VM::toJson(['error' => 'Error patch new product']), 400);
        }

        VM::sendResponse(VM::toJson(['data' => 'Product updated']), 200);
    }

    public function delete(): void
    {
        VM::validateURLParameters($this->params, 2, [CATEGORY, ID]);

        $category = $this->params[CATEGORY];
        $id = (int)$this->params[ID];

        $product = $this->productModel->one($category, $id);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $products = $this->productModel->delete($category, $id);

        $isSaved = VM::saveProducts($products, __DIR__ . LOCATION_PRODUCT);

        if (!$isSaved) {
            VM::sendResponse((VM::toJson(['error' => 'Error deleting product'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Product deleted']), 200);
    }

    public function pay(): void
    {
        VM::validateRequestData(
            [
                ID =>               'int',
                CATEGORY =>         'string',
                PAYMENT_AMOUNT =>   'string'
            ],
            $this->requestBody
        );

        $id = $this->requestBody[ID];
        $category = $this->requestBody[CATEGORY];

        $product = $this->productModel->all($category, $id);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        if ($product[PRICE] > $this->requestBody[PAYMENT_AMOUNT]) {
            VM::sendResponse(VM::toJson(['error' => 'Insufficient pay amount']), 404);
        }

        $change = $this->requestBody[PAYMENT_AMOUNT] - $product[PRICE];

        $receipt = VM::printReceipt(
            $product[NAME],
            $product[PRICE],
            $this->requestBody[PAYMENT_AMOUNT],
            $change
        );

        VM::sendResponse(VM::toJson($receipt), 200);
    }

}