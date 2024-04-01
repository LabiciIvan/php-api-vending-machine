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
        $products = $this->productModel->all();

        VM::sendResponse(VM::toJson(['data' => $products]), 200);
    }

    public function show(): void
    {
        VM::validateURLParameters($this->params, 2, [CATEGORY, ID]);

        $category = $this->params[CATEGORY];
        $id = (int)$this->params[ID];

        $product = $this->productModel->selectOne($category, $id);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        VM::sendResponse(VM::toJson($product), 200);
    }

    public function create()
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

        $categoryProducts = $this->productModel->selectAll($this->requestBody[CATEGORY]);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn\'t exists.']), 404);
        }

        $isCreated = $this->productModel->createProduct($this->requestBody);

        if (!$isCreated) {
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

        $product = $this->productModel->selectOne($this->requestBody[CATEGORY], $this->requestBody[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isUpdated = $this->productModel->updateProduct($product, $this->requestBody);

        if (!$isUpdated) {
            VM::sendResponse((VM::toJson(['error' => 'Error updating new product'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Product updated']), 200);
    }

    public function patch(): void
    {
        $validationError = VM::validateRequestData(
            [
                ID => 'int', 
                CATEGORY => 'string'
            ], 
            $this->requestBody
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }

        $allowedToPatch = [NAME => 'string', PRICE => 'string', QUANTITY => 'int'];

        $typeErrors = VM::validateType($allowedToPatch, $this->requestBody, true);

        if ($typeErrors) {
            VM::sendResponse(VM::toJson(['error' => $typeErrors]), 400);
        }

        $product = $this->productModel->selectOne($this->requestBody[CATEGORY], $this->requestBody[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isPatched = $this->productModel->updateProduct($product, $this->requestBody, true);

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

        $product = $this->productModel->selectOne($category, $id);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isDeleted = $this->productModel->deleteProduct($category, $id);


        if (!$isDeleted) {
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

        $product = $this->productModel->selectAll($category, $id);

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