<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use App\Services\File;
use App\Services\Json;
use App\Services\Generate;
use App\Interfaces\ModelInterface;

class Product implements ModelInterface
{
    private ?array $products;

    const ID = 'id';

    const NAME = 'name';

    const PRICE = 'price';

    const QUANTITY  = 'quantity';

    const CATEGORY_ID = 'category_id';

    const PRODUCTS = 'products';

    public function __construct($path)
    {
        $dataEncoded = File::readFile($path);

        if ($dataEncoded === null) {
            throw new RuntimeException('Failed to open stream: No such file or directory');
        }

        $dataDecoded = Json::fromJson($dataEncoded);

        $this->products = $dataDecoded[self::PRODUCTS];
    }

    public function one(int $id): ?array
    {
        foreach ($this->products as $product) {
            if ($product[self::ID] === $id) {
                return $product;
            }
        }

        return null;
    }

    public function all(): ?array
    {
        return $this->products;
    }

    public function create(array $data): array
    {
        $product = [
            self::ID => Generate::id(
                $this->products,
                self::ID
            ),
            self::CATEGORY_ID => $data[self::CATEGORY_ID],
            self::PRICE => $data[self::PRICE],
            self::NAME => $data[self::NAME],
            self::QUANTITY => $data[self::QUANTITY],
        ];

        $this->products[] = $product;

        return $this->products;
    }

    public function save(string $path, array $products, string $entity): bool
    {
        $dataEncoded = File::readFile($path);

        if ($dataEncoded === null) {
            throw new RuntimeException('Failed to open stream: No such file or directory');
        }

        $dataDecoded = Json::fromJson($dataEncoded);

        if (!isset($dataDecoded[$entity])) {
            throw new RuntimeException("Cannot save data in {$entity} as it does not exist");
        }

        $dataDecoded[$entity] = $products;

        $dataEncoded = Json::toJson($dataDecoded);

        return File::writeFile($path, $dataEncoded);
    }

    public function update(array $updateData, bool $patch = false): array
    {
        $fields = [self::PRICE, self::NAME, self::QUANTITY];

        $id = $updateData[self::ID];

        $categoryID = $updateData[self::CATEGORY_ID];

        foreach ($this->products as &$product) {
            if ($product[self::ID] === $id && $product[self::CATEGORY_ID] === $categoryID) {
                foreach ($fields as $field) {
                    $product[$field] = $patch ? ($updateData[$field] ?? $product[$field]) : $updateData[$field];
                }
            }
        }

        return $this->products;
    }

    public function delete(int $id): array
    {
        $position = null;

        foreach($this->products as $key => $product) {
            if ($product[self::ID] === $id) {
                $position = $key;
            }
        }

        if (!isset($position)) {
            throw new RuntimeException('Product position not found based on the provided id');
        }

        unset($this->products[$position]);

        return $this->products;
    }
}
