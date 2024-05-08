<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;
use App\HttpRequest;
use App\Services\Log;
use App\Services\Json;
use App\Models\Category;
use App\Services\Response;
use App\Validation\ValidateCategory;

class CategoryController
{
    private Category $categoryModel;

    private const PATH_PRODUCTS = __DIR__ . '/../../products.json';

    private const CATEGORY = 'category';

    public function __construct()
    {
        try {
            $this->categoryModel = new Category(self::PATH_PRODUCTS);
        } catch (Exception $e) {
            Log::errors('In CategoryController', $e->getMessage(), __LINE__);
        }
    }

    public function index(): void
    {
        $categories = $this->categoryModel->all();

        Response::send(Json::toJson(['data' => $categories]), 200);
    }

    public function show(HttpRequest $request, ValidateCategory $validator): void
    {
        $params = $request->getParameters();

        if ($params === null) {
            Response::send(Json::toJson(['error' => 'Data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                self::CATEGORY => 'string|required'
            ],
            $params
        );

        if ($validationError) {
            Response::send(Json::toJson($validationError), 400);
        }

        $category = $params[self::CATEGORY];

        $categoryProducts = $this->categoryModel->one($category);

        if ($categoryProducts === null) {
            Response::send(Json::toJson(['error' => 'Category products not found']), 404);
        }

        Response::send(Json::toJson($categoryProducts), 200);
    }

    public function create(HttpRequest $request, ValidateCategory $validator): void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                self::CATEGORY => 'string|required'
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson($validationError), 400);
        }

        $categoryProducts = $this->categoryModel->one($requestData[self::CATEGORY]);

        if ($categoryProducts !== null) {
            Response::send(Json::toJson(['error' => 'Category already exists']), 400);
        }

        try {
            $this->categoryModel->create($requestData);
        } catch (Exception $e) {
            Log::errors('In CategoryController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        $isSaved = $this->categoryModel->save(self::PATH_PRODUCTS);

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error uploading new category']), 400);
        }

        Response::send(Json::toJson(['data' => 'Category successfully created']), 200);
    }

    public function update(HttpRequest $request, ValidateCategory $validator)
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                CATEGORY => 'string|required',
                NEW_CATEGORY => 'string|required'
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson($validationError), 400);
        }

        $category = $this->categoryModel->one($requestData[CATEGORY]);

        if ($category === null) {
            Response::send(Json::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        try {
            $this->categoryModel->update($requestData);
        } catch (Exception $e) {
            Log::errors('In CategoryController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        $isSaved = $this->categoryModel->save(self::PATH_PRODUCTS);

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error updating category']), 500);
        }

        Response::send(Json::toJson(['data' => 'Category updated']), 200);
    }

    public function delete(HttpRequest $request, ValidateCategory $validator):void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                self::CATEGORY => 'string|required',
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson($validationError), 400);
        }

        $categoryProducts = $this->categoryModel->one($requestData[self::CATEGORY]);

        if ($categoryProducts === null) {
            Response::send(Json::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        if ($categoryProducts) {
            Response::send(Json::toJson(['error' => 'Category cant\'t be deleted as it contains products']), 400);
        }

        try {
            $this->categoryModel->delete($requestData[self::CATEGORY]);
        } catch (Exception $e) {
            Log::errors('In CategoryController', $e->getMessage(), __LINE__);

            Response::send(Json::toJson(['error' => 'Internal error, try again later']), 500);
        }

        $isSaved = $this->categoryModel->save(self::PATH_PRODUCTS);

        if (!$isSaved) {
            Response::send(Json::toJson(['error' => 'Error deleting category']), 500);
        }

        Response::send(Json::toJson(['data' => 'Category deleted']), 200);
    }
}
