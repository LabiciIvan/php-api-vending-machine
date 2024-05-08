<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use App\Services\File;
use App\Services\Json;
use App\Interfaces\ModelCategoryInterface;

class Category implements ModelCategoryInterface
{
    private ?array $products;

    private const CATEGORY = 'category';

    private const NEW_CATEGORY = 'newCategory';

    public function __construct($path)
    {
        $encodedProducts = File::readFile($path);

        $this->products = $encodedProducts ? Json::fromJson($encodedProducts) : null;

        if ($this->products === null) {
            throw new RuntimeException('Failed to open stream: No such file or directory');
        }
    }

    public function all(): ?array
    {
        $categories = array_keys($this->products);

        return $categories;
    }
    
    public function one(string $category): ?array
    {
        return $this->products[$category] ?? null;
    }

    public function create(array $data): void
    {
        if (!isset($data[self::CATEGORY])) {
            throw new RuntimeException('Missing `category` key in the array');
        }

        if (isset($this->products[$data[self::CATEGORY]])) {
            throw new RuntimeException('Can\'t create, category already exists');
        }

        $this->products[$data[self::CATEGORY]] = [];
    }

    public function update(array $toUpdate): void
    {
        if (!isset($toUpdate[self::NEW_CATEGORY], $toUpdate[self::CATEGORY])) {
            throw new RuntimeException('Missing `category` or `newCategory` keys in the array');
        }

        if (!isset($this->products[$toUpdate[self::CATEGORY]])) {
            throw new RuntimeException("Category `{$toUpdate[self::CATEGORY]}` does not exist");
        }

        $this->products[$toUpdate[self::NEW_CATEGORY]] = $this->products[$toUpdate[self::CATEGORY]];

        unset($this->products[$toUpdate[self::CATEGORY]]);
    }

    public function delete(string $category): void
    {
        if (isset($this->products[$category]) && count($this->products[$category]) > 0) {
            throw new RuntimeException('Can\'t delete category as it contains products');
        }

        if (!isset($this->products[$category])) {
            throw new RuntimeException("Category `{$category}` does not exist");
        }

        unset($this->products[$category]);
    }

    public function save(string $path): bool
    {
        $encodedProducts = Json::toJson($this->products);

        return File::writeFile($path, $encodedProducts);
    }
}
