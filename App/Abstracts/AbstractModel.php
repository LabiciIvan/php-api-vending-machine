<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Services\Json;
use App\Interfaces\Model\ModelInterface;

abstract class AbstractModel implements ModelInterface
{
    public function getProducts(): ?array
    {
        $products = @file_get_contents(__DIR__ . LOCATION_PRODUCT);

        if ($products === false) {
            return null;
        }

        return Json::fromJson($products);
    }

    public function save(array $data): bool
    {
        if (!file_exists(__DIR__ . LOCATION_PRODUCT)) {
            return false;
        }

        $jsonEncoded = Json::toJson($data);

        $isSaved = file_put_contents(__DIR__ . LOCATION_PRODUCT, $jsonEncoded);

        return $isSaved !== false;
    }

    public function generateId(array $categoryOfProducts, string $idKeyName): int
    {
        $id = 0;

        foreach ($categoryOfProducts as $product) {
            if ($product[$idKeyName] >= $id) {
                $id = $product[$idKeyName] + 1;
            }
        }

        return $id;
    }
}