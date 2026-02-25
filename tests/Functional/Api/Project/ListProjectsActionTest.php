<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Project;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class ListProjectsActionTest extends AppTestCase
{
    public function testReturnsOk(): void
    {
        $request = Request::create('/api/projects', 'GET');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('current_page', $data['meta']);
        $this->assertArrayHasKey('per_page', $data['meta']);
        $this->assertArrayHasKey('total', $data['meta']);
        $this->assertArrayHasKey('last_page', $data['meta']);
    }

    public function testWithFilter(): void
    {
        $this->createTestProject();

        $request = Request::create('/api/projects', 'GET', ['status' => 'development']);
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data['data']);
    }
}
