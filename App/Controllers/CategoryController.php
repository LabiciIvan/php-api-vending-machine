<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Request;
use App\Services\Json;
use App\Models\Category;
use App\Services\Response;
use App\Validation\ValidateCategory;

class CategoryController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->selectAll();

        Response::send(Json::toJson(['data' => $categories]), 200);
    }

    public function show(Request $request, ValidateCategory $validator): void
    {
        $params = $request->getParameter();

        if ($params === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                CATEGORY => 'string|required'
            ],
            $params
        );

        if ($validationError) {
            Response::send(Json::toJson($validator->getErrors()), 400);
        }

        $category = $params[CATEGORY];

        $categoryProducts = $this->categoryModel->select($category);

        if ($categoryProducts === null) {
            Response::send(Json::toJson(['error' => 'Category products not found']), 404);
        }

        Response::send(Json::toJson($categoryProducts), 200);
    }

    public function create(Request $request, ValidateCategory $validator): void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                CATEGORY => 'string|required'
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson($validator->getErrors()), 400);
        }

        $categoryProducts = $this->categoryModel->select($requestData[CATEGORY]);

        if ($categoryProducts !== null) {
            Response::send(Json::toJson(['error' => 'Category already exists']), 400);
        }

        $isCreated = $this->categoryModel->createCategory($requestData[CATEGORY]);

        if (!$isCreated) {
            Response::send(Json::toJson(['error' => 'Error uploading new category']), 400);
        }

        Response::send(Json::toJson(['data' => 'Category successfully created']), 200);
    }

    public function update(Request $request, ValidateCategory $validator)
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
            Response::send(Json::toJson($validator->getErrors()), 400);
        }

        $categoryProducts = $this->categoryModel->select($requestData[CATEGORY]);

        if ($categoryProducts === null) {
            Response::send(Json::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        $isUpdated = $this->categoryModel->updateCategory($requestData);

        if (!$isUpdated) {
            Response::send(Json::toJson(['error' => 'Error updating category']), 500);
        }

        Response::send(Json::toJson(['data' => 'Category updated']), 200);
    }

    public function delete(Request $request, ValidateCategory $validator):void
    {
        $requestData = $request->getData();

        if ($requestData === null) {
            Response::send(Json::toJson(['error' => 'Submitted data could not be read']), 500);
        }

        $validationError = $validator->validate(
            [
                CATEGORY => 'string|required',
            ],
            $requestData
        );

        if ($validationError) {
            Response::send(Json::toJson($validator->getErrors()), 400);
        }

        $categoryProducts = $this->categoryModel->select($requestData[CATEGORY]);

        if ($categoryProducts === null) {
            Response::send(Json::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        if ($categoryProducts) {
            Response::send(Json::toJson(['error' => 'Category cant\'t be deleted as it contains products']), 400);
        }

        $isDeleted = $this->categoryModel->deleteCategory($requestData[CATEGORY]);

        if (!$isDeleted) {
            Response::send(Json::toJson(['error' => 'Error deleting category']), 500);
        }

        Response::send(Json::toJson(['data' => 'Category deleted']), 200);
    }
}