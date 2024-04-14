<?php

declare(strict_types=1);

namespace App\Interfaces\Request;

interface RequestParametersInterface
{
    public function readParameters(string $query): ?array;
}