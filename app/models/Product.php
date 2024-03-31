<?php

namespace App\Models;

use App\Services\Database;
use App\VM;

class Product extends Database {

    private ?array $products;

    public function __construct()
    {
        parent::__construct();

        $this->products = $this->getProducts();
    }

    public function all(string $category): ?array
    {
        return $this->products[$category] ?? null;
    }

    public function one(string $category, int $id): ?array
    {
        if (!isset($this->products[$category])) {
            return null;
        }

        foreach ($this->products[$category] as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }

        return null;
    }

    public function create(array $requestBody): array
    {
        return [
            ID => VM::generateId($this->products[$requestBody[CATEGORY]], ID),
            PRICE => $requestBody[PRICE],
            NAME => $requestBody[NAME],
            QUANTITY => $requestBody[QUANTITY],
        ];
    }

    public function update(array $updateProduct, array $newData, string $path, bool $patch = false): bool
    {
        $fields = [PRICE, NAME, QUANTITY];

        foreach ($fields as $field) {
            $updateProduct[$field] = $patch ? ($newData[$field] ?? $updateProduct[$field]) : $newData[$field];
        }

        $position = null;

        foreach ($this->products[$newData[CATEGORY]] as $key => $product) {
            if ($product[ID] === $newData[ID]) {
                $position = $key;
                break;
            }
        }

        $this->products[$newData[CATEGORY]][$position] = $updateProduct;

        return $this->save($this->products, $path);
    }

    public function storeProduct(array $product, string $category, string $path): bool
    {
        array_push($this->products[$category], $product);

        return $this->save($this->products, $path);
    }

    public function delete(string $category, int $id): array
    {
        foreach($this->products[$category] as $key => $product) {
            if ($product['id'] === $id) {
                $position = $key;
            }
        }

        unset($this->products[$category][$position]);

        return $this->products;
    }
}