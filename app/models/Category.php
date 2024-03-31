<?php

namespace App\Models;

use App\Services\Database;

class Category extends Database {

    private ?array $productsCategory;

    public function __construct()
    {
        parent::__construct();

        $this->productsCategory = $this->getProducts();
    }

    public function all(string $category): ?array
    {
        if (!isset($this->productsCategory[$category])) {
            return null;
        }

        return $this->productsCategory[$category];
    }

    public function storeCategory(array $newCategory,string $path): bool
    {
        $this->productsCategory = $newCategory;

        return $this->save($this->productsCategory, $path);
    }
}