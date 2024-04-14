<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected Router $router;

    public function setUp(): void
    {
        $this->router = new Router;
    }

    public function testRoutesAreRegistered(): void
    {
        $routes = $this->router->getRoutes();

        $this->assertCount(5, $routes);
    }

    public function testGetRouteHasExpectedNumberOfElements(): void
    {
        $routes = $this->router->getRoutes();

        $getRoutes = $routes['GET'];

        $this->assertCount(4, $getRoutes);
    }

    public function testPostRouteHasExpectedNumberOfElements(): void
    {
        $routes = $this->router->getRoutes();

        $getRoutes = $routes['POST'];

        $this->assertCount(3, $getRoutes);
    }

    public function testPutRouteHasExpectedNumberOfElements(): void
    {
        $routes = $this->router->getRoutes();

        $getRoutes = $routes['PUT'];

        $this->assertCount(2, $getRoutes);
    }

    public function testPatchRouteHasExpectedNumberOfElements(): void
    {
        $routes = $this->router->getRoutes();

        $getRoutes = $routes['PATCH'];

        $this->assertCount(1, $getRoutes);
    }

    public function testDeleteRouteHasExpectedNumberOfElements(): void
    {
        $routes = $this->router->getRoutes();

        $getRoutes = $routes['DELETE'];

        $this->assertCount(2, $getRoutes);
    }

}