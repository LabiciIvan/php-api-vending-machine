<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;
use App\HttpRequest;
use App\Services\Log;
use App\Services\Json;
use App\Models\Product;
use App\Services\Response;
use App\Validation\ValidateProduct;

class ProductController
{
    private Product $productModel;

    const PATH_PRODUCTS = __DIR__ . '/../../products.json';

    const ID = 'id';

    const NAME = 'name';

    const PRICE = 'price';

    const PRODUCTS = 'products';

    const CATEGORY_ID = 'category_id';

    const PAYMENT_AMOUNT = 'paymentAmount';

    public function __construct()
    {
        try {
            $this->productModel = new Product(self::PATH_PRODUCTS);
        } catch (Exception $e) {
            Log::errors('In ProductController', $e->getMessage(), __LINE__);

            exit('A critical error occurred. Execution stopped.');
        }
    }

    public function index(): void
    {
        $products = $this->productModel->all();

        Response::send(Json::toJson(['data' => $products]), 200);
    }

    public function show(HttpRequest $request, ValidateProduct $validator): void
    {
        $params = $request->getParameters();

        if ($params === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                self::ID => 'string|required',
            ],
            $params
        );

        if ($validationError) {
            Response::send(Json::toJson($validationError), 400);
        }

        $product = $this->productModel->one((int)$params[self::ID]);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        Response::send(Json::toJson($product), 200);
    }

    public function create(HttpRequest $request, ValidateProduct $validator)
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate($validator->rulesCreate, $requestData);

        if ($validationError) {
            Response::send(Json::toJson($validationError), 400);
        }

        $products = $this->productModel->create($requestData);

        try {
            $isSaved = $this->productModel->save(self::PATH_PRODUCTS, $products, self::PRODUCTS);
        } catch (Exception $e) {
            Log::errors('In ProductController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error uploading new product']), 500);
        }

        Response::send(Json::toJson(['data' => "Product uploaded."]), 200);
    }

    public function update(HttpRequest $request, ValidateProduct $validator)
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate($validator->rulesUpdate, $requestData);

        if ($validationError) {
            Response::send(Json::toJson([$validationError]), 400);
        }

        $product = $this->productModel->one($requestData[self::ID]);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        $products = $this->productModel->update($requestData);

        try {
            $isSaved = $this->productModel->save(self::PATH_PRODUCTS, $products, self::PRODUCTS);
        } catch (Exception $e) {
            Log::errors('In ProductController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error updating new product']), 500);
        }

        Response::send(Json::toJson(['data' => 'Product updated']), 200);
    }

    public function patch(HttpRequest $request, ValidateProduct $validator): void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate($validator->rulesPatch, $requestData);

        if ($validationError) {
            Response::send(Json::toJson([$validationError]), 400);
        }

        $product = $this->productModel->one($requestData[self::ID]);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        $products = $this->productModel->update($requestData, true);

        try {
            $isSaved = $this->productModel->save(self::PATH_PRODUCTS, $products, self::PRODUCTS);
        } catch (Exception $e) {
            Log::errors('In ProductController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error patch new product']), 400);
        }

        Response::send(Json::toJson(['data' => 'Product updated']), 200);
    }

    public function delete(HttpRequest $request): void
    {
        $requestParams = $request->getParameters();

        if ($requestParams === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $id = isset($requestParams[self::ID]) ? (int)$requestParams[self::ID] : null;

        if ($id === null) {
            Response::send(Json::toJson(['error' => 'Invalid URL parameter to this route']), 403);
        }

        $product = $this->productModel->one($id);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        $products = $this->productModel->delete($id);

        try {
            $isSaved = $this->productModel->save(self::PATH_PRODUCTS, $products, self::PRODUCTS);
        } catch (Exception $e) {
            Log::errors('In ProductController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error deleting product']), 500);
        }

        Response::send(Json::toJson(['data' => 'Product deleted']), 200);
    }

    public function pay(HttpRequest $request, ValidateProduct $validator): void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }
        
        $validationError = $validator->validate(
            [
                self::ID => 'int|required',
                self::CATEGORY_ID => 'string|required',
                self::PAYMENT_AMOUNT => 'string|required'
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson([$validationError]), 400);
        }

        $id = $requestData[self::ID];
        $category = $requestData[self::CATEGORY_ID];

        $product = $this->productModel->one((int)$id);

        if ($product === null) {
            Response::send(Json::toJson(['error' => 'Product not found']), 404);
        }

        if ($product[self::PRICE] > $requestData[self::PAYMENT_AMOUNT]) {
            Response::send(Json::toJson(['error' => 'Insufficient pay amount']), 404);
        }

        $change = $requestData[self::PAYMENT_AMOUNT] - $product[self::PRICE];

        $receipt = [
            'name' => $product[self::NAME],
            'price' => $product[self::PRICE],
            'paid' => number_format($requestData[self::PAYMENT_AMOUNT], 2, '.'),
            'change' => number_format($change, 2, '.')
        ];

        Response::send(Json::toJson($receipt), 200);
    }
}
