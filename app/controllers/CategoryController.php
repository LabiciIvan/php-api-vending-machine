<?php

namespace App\Controllers;

use App\VM;
use App\Models\Category;
use App\Requests\CategoryRequest;

class CategoryController {

    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->selectAll();

        VM::sendResponse(VM::toJson(['data' => $categories]), 200);
    }

    public function show(CategoryRequest $request): void
    {
        $request->validateShow();

        $category = $request->params[CATEGORY];

        $categoryProducts = $this->categoryModel->select($category);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category products not found']), 404);
        }

        VM::sendResponse(VM::toJson($categoryProducts), 200);
    }

    public function create(CategoryRequest $request): void
    {
        $request->validateCreate();

        $categoryProducts = $this->categoryModel->select($request->body[CATEGORY]);

        if ($categoryProducts !== null) {
            VM::sendResponse(VM::toJson(['error' => 'Category already exists']), 400);
        }

        $isCreated = $this->categoryModel->createCategory($request->body[CATEGORY]);

        if (!$isCreated) {
            VM::sendResponse(VM::toJson(['error' => 'Error uploading new category']), 400);
        }

        VM::sendResponse(VM::toJson(['data' => 'Category successfully created']), 200);
    }

    public function update(CategoryRequest $request)
    {
        $request->validateUpdate();

        $categoryProducts = $this->categoryModel->select($request->body[CATEGORY]);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        $isUpdated = $this->categoryModel->updateCategory($request->body);

        if (!$isUpdated) {
            VM::sendResponse((VM::toJson(['error' => 'Error updating category'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Category updated']), 200);
    }

    public function delete(CategoryRequest $request):void
    {
        $request->validateDelete();

        $categoryProducts = $this->categoryModel->select($request->body[CATEGORY]);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category doesn\'t exists']), 400);
        }

        if ($categoryProducts) {
            VM::sendResponse(VM::toJson(['error' => 'Category cant\'t be deleted as it contains products']), 400);
        }

        $isDeleted = $this->categoryModel->deleteCategory($request->body[CATEGORY]);

        if (!$isDeleted) {
            VM::sendResponse((VM::toJson(['error' => 'Error deleting category'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Category deleted']), 200);
    }
}