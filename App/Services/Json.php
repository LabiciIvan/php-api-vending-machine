<?php

namespace App\Services;

use JsonException;
use App\Services\Log;

class Json
{
    public static function toJson(array $data): ?string
    {
        try {
            $jsonEncoded = json_encode($data, JSON_THROW_ON_ERROR, 512);
        } catch (JsonException $e) {
            Log::errors('Error encoding json', $e->getMessage(), __LINE__);
            return null;
        }

        return $jsonEncoded;
    }

    public static function fromJson(string $data): ?array
    {
        try {
            $jsonDecoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::errors('Error decoding json', $e->getMessage(), __LINE__);
            return null;
        }

        return $jsonDecoded;
    }
}