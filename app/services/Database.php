<?php

namespace App\Services;

use App\VM;

class Database {

    protected array $data;

    public function __construct()
    {
        $this->data = VM::readProducts(__DIR__ . LOCATION_PRODUCT);
    }

    protected function getProducts(): ?array
    {
        return $this->data;
    }

    protected function save(array $products): bool
    {
        return VM::saveProducts($products, __DIR__ . LOCATION_PRODUCT);
    }
}