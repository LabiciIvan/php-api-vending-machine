<?php

declare(strict_types=1);

namespace App\Models;

use App\Abstracts\AbstractModel;

class Category extends AbstractModel
{
    private ?array $productsCategory;

    public function __construct()
    {
        $this->productsCategory = $this->getProducts();
    }

    public function select(string $category): ?array
    {
        if (!isset($this->productsCategory[$category])) {
            return null;
        }

        return $this->productsCategory[$category];
    }

    public function selectAll(): array
    {
        return array_keys($this->productsCategory);
    }

    public function storeCategory(array $newCategory): bool
    {
        $this->productsCategory = $newCategory;

        return $this->save($this->productsCategory);
    }

    public function createCategory(string $categoryName): bool
    {
        $this->productsCategory[$categoryName] = [];

        return $this->save($this->productsCategory);
    }

    public function updateCategory(array $requestBody): bool
    {
        $temporaryProducts = array_values($this->productsCategory);

        $temporaryKeys = array_keys($this->productsCategory);

        foreach ($temporaryKeys as &$key) {
            if ($key === $requestBody[CATEGORY]) {
                $key = $requestBody[NEW_CATEGORY];
                break;
            }
        }

        $this->productsCategory = array_combine($temporaryKeys, $temporaryProducts);

        return $this->save($this->productsCategory);
    }

    public function deleteCategory($categoryName): bool
    {
        unset($this->productsCategory[$categoryName]);

        return $this->save($this->productsCategory);
    }
}