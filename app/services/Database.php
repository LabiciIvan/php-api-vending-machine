<?php

namespace App\Services;

use App\VM;

class Database {

    protected array $data;

    public function __construct()
    {
        $this->data = VM::readProducts(__DIR__ . '/../../products.json');
    }

    protected function getProducts(): ?array
    {
        return $this->data;
    }

    protected function save(array $products, $path): bool
    {
        return VM::saveProducts($products, $path);
    }
}