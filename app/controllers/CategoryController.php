<?php

namespace App\Controllers;

use App\Models\Category;
use App\VM;

class CategoryController {

    private ?array $params;

    private ?array $requestBody;

    private Category $categoryModel;

    public function __construct(?array $params, ?array $requestBody)
    {
        $this->params = $params;
        $this->requestBody = $requestBody;

        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->selectAll();

        VM::sendResponse(VM::toJson(['data' => $categories]), 200);
    }

    public function show(): void
    {
        VM::validateURLParameters($this->params, 1, [CATEGORY]);

        $category = $this->params[CATEGORY];
    
        $categoryProducts = $this->categoryModel->select($category);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category products not found']), 404);
        }

        VM::sendResponse(VM::toJson($categoryProducts), 200);
    }

    public function create(): void
    {
        if ($this->requestBody === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateRequestData(
            [
                CATEGORY => 'string'
            ],
            $this->requestBody
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }

        $categoryProducts = $this->categoryModel->select($this->requestBody[CATEGORY]);

        if ($categoryProducts !== null) {
            VM::sendResponse(VM::toJson(['error' => 'Category already exists']), 400);
        }

        $isCreated = $this->categoryModel->createCategory($this->requestBody[CATEGORY]);

        if (!$isCreated) {
            VM::sendResponse(VM::toJson(['error' => 'Error uploading new category']), 400);
        }

        VM::sendResponse(VM::toJson(['data' => 'Category successfully created']), 200);
    }

    public function update()
    {
        if ($this->requestBody === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateRequestData(
            [
                CATEGORY        => 'string',
                NEW_CATEGORY    => 'string',
            ],
            $this->requestBody
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }

        $categoryProducts = $this->categoryModel->select($this->requestBody[CATEGORY]);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        $isUpdated = $this->categoryModel->updateCategory($this->requestBody);

        if (!$isUpdated) {
            VM::sendResponse((VM::toJson(['error' => 'Error updating category'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Category updated']), 200);
    }

    public function delete():void
    {
        if ($this->requestBody === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateRequestData(
            [
                CATEGORY => 'string'
            ],
            $this->requestBody
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }

        $categoryProducts = $this->categoryModel->select($this->requestBody[CATEGORY]);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        if ($categoryProducts) {
            VM::sendResponse(VM::toJson(['error' => 'Category cant\'t be deleted as contains products']), 400);
        }

        $isDeleted = $this->categoryModel->deleteCategory($this->requestBody[CATEGORY]);

        if (!$isDeleted) {
            VM::sendResponse((VM::toJson(['error' => 'Error deleting category'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Category deleted']), 200);
    }
}