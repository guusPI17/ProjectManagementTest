<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Platform;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class ListPlatformsActionTest extends AppTestCase
{
    public function testReturnsOk(): void
    {
        $request = Request::create('/api/platforms', 'GET');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertArrayHasKey('meta', $data);
    }

    public function testWithFilter(): void
    {
        $request = Request::create('/api/platforms', 'GET', ['code' => 'wordpress']);
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data['data']);
    }
}
