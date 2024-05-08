<?php

declare(strict_types=1);

namespace App\Services;

class Generate
{
    public static function id(array $categoryOfProducts, string $keyID): int
    {
        $id = 0;

        foreach ($categoryOfProducts as $product) {
            if ($product[$keyID] >= $id) {
                $id = $product[$keyID] + 1;
            }
        }

        return $id;
    }
}