<?php

namespace App\Interfaces;

interface RoutesManagementInterface
{
    public function getRoutes(): array;

    public function setRoutes(): void;
}
