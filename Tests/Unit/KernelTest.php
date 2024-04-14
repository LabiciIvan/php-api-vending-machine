<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Kernel;
use App\Request;
use App\Router;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{

    private Kernel $kernel;

    public function setUp(): void
    {
        parent::setUp();

        $this->kernel = new Kernel();
    }

    /**
     * @test
     */
    public function checkInstanceIsRequest(): void
    {
        $requestInstance = new Request();

        $this->assertInstanceOf($requestInstance::class, $this->kernel->request);
    }

    /**
     * @test
     */
    public function checkInstanceIsRouter(): void
    {
        $requestInstance = new Router();

        $this->assertInstanceOf($requestInstance::class, $this->kernel->router);
    }

    /**
     * @test
     */
    public function checkRoutesThruRouterFromKernelIsArray(): void
    {
        $routes = $this->kernel->router->getRoutes();

        $this->assertIsArray($routes);
    }

    /**
     * @test
     */
    public function checkRoutesHaveAllMethodsInRoutesArray(): void
    {
        $routes = $this->kernel->router->getRoutes();

        $availableMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        foreach ($availableMethods as $method) {
            $this->assertArrayHasKey($method, $routes);
        }

    }
}