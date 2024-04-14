<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Request;
use App\Services\Json;
use App\Models\Product;
use App\Services\Response;
use App\Validation\ValidateProduct;

class ProductController
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index(): void
    {
        $products = $this->productModel->all();

        Response::send(Json::toJson(['data' => $products]), 200);
    }

    public function show(Request $request, ValidateProduct $validator): void
    {
        $params = $request->getParameter();

        if ($params === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                CATEGORY    => 'string|required',
                ID          => 'string|required',
            ],
            $params
        );

        if ($validationError) {
            Response::send(Json::toJson($validator->getErrors()), 400);
        }

        $product = $this->productModel->selectOne($params[CATEGORY], (int)$params[ID]);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        Response::send(Json::toJson($product), 200);
    }

    public function create(Request $request, ValidateProduct $validator)
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate($validator->rulesCreate, $requestData);

        if ($validationError) {
            Response::send(Json::toJson($validator->getErrors()), 400);
        }

        $categoryProducts = $this->productModel->selectAll($requestData[CATEGORY]);

        if ($categoryProducts === null) {
            Response::send(Json::toJson(['error' => 'Category doesn\'t exists.']), 404);
        }

        $isCreated = $this->productModel->createProduct($requestData);

        if (!$isCreated) {
            Response::send(Json::toJson(['error' => 'Error uploading new product']), 500);
        }

        Response::send(Json::toJson(['data' => 'Product uploaded.']), 200);
    }

    public function update(Request $request, ValidateProduct $validator)
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate($validator->rulesUpdate, $requestData);

        if ($validationError) {
            Response::send(Json::toJson([$validator->getErrors()]), 400);
        }

        $product = $this->productModel->selectOne($requestData[CATEGORY], $requestData[ID]);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        $isUpdated = $this->productModel->updateProduct($product,$requestData);

        if (!$isUpdated) {
            Response::send(Json::toJson(['error' => 'Error updating new product']), 500);
        }

        Response::send(Json::toJson(['data' => 'Product updated']), 200);
    }

    public function patch(Request $request, ValidateProduct $validator): void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate($validator->rulesPatch, $requestData);

        if ($validationError) {
            Response::send(Json::toJson([$validator->getErrors()]), 400);
        }

        $product = $this->productModel->selectOne($requestData[CATEGORY], $requestData[ID]);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        $isPatched = $this->productModel->updateProduct($product, $requestData, true);

        if (!$isPatched) {
            Response::send(Json::toJson(['error' => 'Error patch new product']), 400);
        }

        Response::send(Json::toJson(['data' => 'Product updated']), 200);
    }

    public function delete(Request $request): void
    {
        $requestParams = $request->getParameter();

        if ($requestParams === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $category = $requestParams[CATEGORY];
        $id = (int)$requestParams[ID];

        $product = $this->productModel->selectOne($category, $id);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        $isDeleted = $this->productModel->deleteProduct($category, $id);

        if (!$isDeleted) {
            Response::send(Json::toJson(['error' => 'Error deleting product']), 500);
        }

        Response::send(Json::toJson(['data' => 'Product deleted']), 200);
    }

    public function pay(Request $request, ValidateProduct $validator): void
    {

        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }
        
        $validationError = $validator->validate(
            [
                ID              => 'int|required',
                CATEGORY        => 'string|required',
                PAYMENT_AMOUNT  => 'string|required'
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson([$validator->getErrors()]), 400);
        }

        $id = $requestData[ID];
        $category = $requestData[CATEGORY];

        $product = $this->productModel->selectAll($category, $id);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        if ($product[PRICE] > $requestData[PAYMENT_AMOUNT]) {
            Response::send(Json::toJson(['error' => 'Insufficient pay amount']), 404);
        }

        $change = $requestData[PAYMENT_AMOUNT] - $product[PRICE];

        $receipt = [
            'name' => $product[NAME],
            'price' => $product[PRICE],
            'paid' => number_format($requestData[PAYMENT_AMOUNT], 2, '.'),
            'change' => number_format($change, 2, '.')
        ];

        Response::send(Json::toJson($receipt), 200);
    }

}