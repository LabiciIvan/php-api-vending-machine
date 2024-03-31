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

    public function index()
    {
        VM::validateURLParameters($this->params, 1, [CATEGORY]);

        $category = $this->params[CATEGORY];
    
        $categoryProducts = $this->categoryModel->all($category);

        if ($categoryProducts === null) {
            VM::sendResponse(VM::toJson(['error' => 'Category products not found']), 404);
        }

        VM::sendResponse(VM::toJson($categoryProducts), 200);
    }

    public function upload()
    {
        if ($this->requestBody === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateProducts(
            $this->requestBody,
            [
                ID =>       'int',
                QUANTITY => 'int',
                NAME =>     'string',
                PRICE =>    'string'
            ]
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }

        $isStored = $this->categoryModel->storeCategory($this->requestBody, __DIR__ . LOCATION_PRODUCT);

        if (!$isStored) {
            VM::sendResponse((VM::toJson(['error' => 'Error uploading new products'])), 500);
        }

        VM::sendResponse(VM::toJson(['data' => 'Products Uploaded']), 200);
    }
}