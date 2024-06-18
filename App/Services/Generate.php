<?php

declare(strict_types=1);

namespace App\Services;

class Generate
{
    public static function id(array $data, string $keyID): int
    {
        $id = 0;

        foreach ($data as $item) {
            if ($item[$keyID] >= $id) {
                $id = $item[$keyID] + 1;
            }
        }

        return $id;
    }
}