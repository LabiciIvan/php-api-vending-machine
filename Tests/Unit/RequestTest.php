<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    private Request $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->request = new Request();
    }

    public function testGetMethodReturnsMethod():void
    {
        $method = $this->request->getMethod();

        $this->assertSame('GET', $method);
    }


    public function testEndpointIsReturned(): void
    {
        $endpoint = $this->request->getEndpoint();

        $this->assertSame('/test/', $endpoint);
    }

    public function testParamsAreReturnedAndNotEmpty(): void
    {

        $params = $this->request->getParameter();

        $this->assertIsArray($params);
        $this->assertNotEmpty($params);
    }

    public function testReturnedParamsHaveSuiteAndIdKeys(): void
    {
        $params = $this->request->getParameter();

        $paramKeys = ['suite', 'id'];

        foreach ($paramKeys as $key) {
            $this->assertArrayHasKey($key, $params);
        }
    }

    public function testGetDataMethodReturnsNull(): void
    {
        $requestData = $this->request->getData();

        $this->assertNull($requestData);
    }
}
