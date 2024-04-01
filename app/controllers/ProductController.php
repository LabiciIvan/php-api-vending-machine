<?php

namespace App\Controllers;

use App\VM;
use App\Models\Product;
use App\Requests\ProductRequest;

class ProductController {

    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index(): void
    {
        $products = $this->productModel->all();

        VM::sendResponse(VM::toJson(['data' => $products]), 200);
    }

    public function show(ProductRequest $request): void
    {
        $request->validateShow();

        $product = $this->productModel->selectOne($request->params[CATEGORY], (int)$request->params[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        VM::sendResponse(VM::toJson($product), 200);
    }

    public function create(ProductRequest $request)
    {
        $request->validateCreate();

        $categoryProducts = $this->productModel->selectAll($request->body[CATEGORY]);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn\'t exists.']), 404);
        }

        $isCreated = $this->productModel->createProduct($request->body);

        if (!$isCreated) {
            VM::sendResponse((VM::toJson(['error' => 'Error uploading new product'])), 500);
        }

        VM::sendResponse((VM::toJson(['data' => 'Product uploaded.'])), 200);
    }

    public function update(ProductRequest $request)
    {
        $request->validateUpdate();

        $product = $this->productModel->selectOne($request->body[CATEGORY], $request->body[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isUpdated = $this->productModel->updateProduct($product, $request->body);

        if (!$isUpdated) {
            VM::sendResponse((VM::toJson(['error' => 'Error updating new product'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Product updated']), 200);
    }

    public function patch(ProductRequest $request): void
    {
        $request->validatePatch();

        $product = $this->productModel->selectOne($request->body[CATEGORY], $request->body[ID]);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        $isPatched = $this->productModel->updateProduct($product, $request->body, true);

        if (!$isPatched) {
            VM::sendResponse(VM::toJson(['error' => 'Error patch new product']), 400);
        }

        VM::sendResponse(VM::toJson(['data' => 'Product updated']), 200);
    }

    public function delete(ProductRequest $request): void
    {
        $request->validateDelete();

        $category = $request->params[CATEGORY];
        $id = (int)$request->params[ID];

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

    public function pay(ProductRequest $request): void
    {
        $request->validatePay();

        $id = $request->body[ID];
        $category = $request->body[CATEGORY];

        $product = $this->productModel->selectAll($category, $id);

        if ($product === null) {
            VM::sendResponse(VM::toJson(['error' => 'Product not found']), 404);
        }

        if ($product[PRICE] > $request->body[PAYMENT_AMOUNT]) {
            VM::sendResponse(VM::toJson(['error' => 'Insufficient pay amount']), 404);
        }

        $change = $request->body[PAYMENT_AMOUNT] - $product[PRICE];

        $receipt = VM::printReceipt(
            $product[NAME],
            $product[PRICE],
            $request->body[PAYMENT_AMOUNT],
            $change
        );

        VM::sendResponse(VM::toJson($receipt), 200);
    }

}