<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;
use App\Services\File;
use App\Services\Json;
use App\Services\Generate;
use App\Interfaces\ModelInterface;

class Category implements ModelInterface
{
    private ?array $category;

    const ID = 'id';

    const NAME = 'name';

    const CATEGORIES = 'categories';

    public function __construct($path)
    {
        $dataEncoded = File::readFile($path);

        if ($dataEncoded === null) {
            throw new RuntimeException('Failed to open stream: No such file or directory');
        }

        $dataDecoded = Json::fromJson($dataEncoded);

        $this->category = $dataDecoded[self::CATEGORIES];
    }

    public function one(int $id): ?array
    {
        foreach ($this->category as $category) {
            if ($category[self::ID] === $id) {
                return $category;
            }
        }

        return null;
    }

    public function all(): ?array
    {
        return $this->category;
    }

    public function create(array $data): array
    {
        $category = [
            self::ID => Generate::id($this->category, self::ID),
            self::NAME => $data[self::NAME]
        ];

        $this->category[] = $category;

        return $this->category;
    }

    public function update(array $updateData, bool $patch = false): array
    {
        $fields = [self::NAME];

        $id = $updateData[self::ID];

        foreach ($this->category as &$category) {
            if ($category[self::ID] === $id) {
                foreach ($fields as $field) {
                    $category[$field] = $patch ? ($updateData[$field] ?? $category[$field]) : $updateData[$field];
                }
            }
        }

        return $this->category;
    }

    public function delete(int $id): array
    {
        $position = null;

        foreach($this->category as $key => $item) {
            if ($item[self::ID] === $id) {
                $position = $key;
            }
        }

        unset($this->category[$position]);

        return $this->category;
    }

    public function save(string $path, array $categories, string $entity): bool
    {
        $dataEncoded = File::readFile($path);

        if ($dataEncoded === null) {
            throw new RuntimeException('Failed to open stream: No such file or directory');
        }

        $dataDecoded = Json::fromJson($dataEncoded);

        $dataDecoded[$entity] = $categories;

        $dataEncoded = Json::toJson($dataDecoded);

        return File::writeFile($path, $dataEncoded);
    }
}
