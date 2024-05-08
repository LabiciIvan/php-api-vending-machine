<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use App\Services\File;
use App\Services\Json;
use App\Services\Generate;
use App\Interfaces\ModelProductInterface;

class Product implements ModelProductInterface
{
    private ?array $products;

    private string $idKey = 'id';

    private string $priceKey = 'price';

    private string $nameKey = 'name';

    private string $quantityKey = 'quantity';

    private string $categoryKey = 'category';

    public function __construct($path)
    {
        $encodedProducts = File::readFile($path);

        if ($encodedProducts === null) {
            throw new RuntimeException('Failed to open stream: No such file or directory');
        }

        $this->products = $encodedProducts ? Json::fromJson($encodedProducts) : null;
    }

    public function one(string $category, int $id): ?array
    {
        if (!isset($this->products[$category])) {
            return null;
        }

        foreach ($this->products[$category] as $product) {
            if ($product[$this->idKey] == $id) {
                return $product;
            }
        }

        return null;
    }

    public function all(): ?array
    {
        return $this->products;
    }

    public function create(array $data): int
    {
        if (!isset($data[$this->categoryKey])) {
            throw new RuntimeException("Missing `{$this->categoryKey}` key in the array");
        }

        if (!isset($this->products[$data[$this->categoryKey]])) {
            throw new RuntimeException("Category `{$data[$this->categoryKey]}` does not exist");
        }

        $product = [
            $this->idKey => Generate::id(
                $this->products[$data[$this->categoryKey]],
                $this->idKey
            ),
            $this->priceKey => $data[$this->priceKey],
            $this->nameKey => $data[$this->nameKey],
            $this->quantityKey => $data[$this->quantityKey],
        ];

        array_push($this->products[$data[$this->categoryKey]], $product);

        return $product[$this->idKey];
    }

    public function save(string $path): bool
    {
        if (!file_exists($path)) {
            throw new RuntimeException('Could not find file in which to save data');
        }

        $toJsonString = Json::toJson($this->products);

        $isSaved = File::writeFile($path, $toJsonString);

        return $isSaved;
    }

    /**
     * @throws RuntimeException Product position not found based on the provided id
     */
    public function update(array $updateProduct, array $newData = null, bool $patch = false): void
    {
        $fields = [$this->priceKey, $this->nameKey, $this->quantityKey];

        foreach ($fields as $field) {
            $updateProduct[$field] = $patch ? ($newData[$field] ?? $updateProduct[$field]) : $newData[$field];
        }

        $position = null;

        foreach ($this->products[$newData[$this->categoryKey]] as $key => $product) {
            if ($product[$this->idKey] === $newData[$this->idKey]) {
                $position = $key;
                break;
            }
        }

        if ($position === null) {
            throw new RuntimeException('Product position not found based on the provided id');
        }

        $this->products[$newData[$this->categoryKey]][$position] = $updateProduct;
    }

    public function delete(string $category = null, int $id = null): void
    {
        foreach($this->products[$category] as $key => $product) {
            if ($product[$this->idKey] === $id) {
                $position = $key;
            }
        }

        if (!isset($position)) {
            throw new RuntimeException('Product position not found based on the provided id');
        }

        unset($this->products[$category][$position]);
    }

    public function existCategory(string $category): bool
    {
        return key_exists($category, $this->products);
    }
}
