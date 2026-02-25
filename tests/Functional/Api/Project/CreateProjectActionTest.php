<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Project;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class CreateProjectActionTest extends AppTestCase
{
    public function testReturns201(): void
    {
        $request = Request::create('/api/projects', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Project',
            'url' => 'https://example.com',
            'platform_id' => 1,
            'status_id' => 1,
            'description' => 'Test description',
        ]));

        $response = $this->kernel->handle($request);

        $this->assertSame(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertSame('Test Project', $data['data']['name']);
        $this->assertSame('https://example.com', $data['data']['url']);
        $this->assertArrayHasKey('platform', $data['data']);
        $this->assertArrayHasKey('status', $data['data']);
        $this->assertIsArray($data['data']['platform']);
        $this->assertIsArray($data['data']['status']);
    }

    public function testValidationError(): void
    {
        $request = Request::create('/api/projects', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => '',
            'url' => 'not-valid',
        ]));

        $response = $this->kernel->handle($request);

        $this->assertSame(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['error']);
    }

    public function testDescriptionTooLong(): void
    {
        $request = Request::create('/api/projects', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Project',
            'url' => 'https://example.com',
            'platform_id' => 1,
            'status_id' => 1,
            'description' => str_repeat('a', 2001),
        ]));

        $response = $this->kernel->handle($request);

        $this->assertSame(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['error']);
    }
}
