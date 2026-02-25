<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Project;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class UpdateProjectActionTest extends AppTestCase
{
    public function testReturnsUpdated(): void
    {
        $projectId = $this->createTestProject();

        $request = Request::create("/api/projects/{$projectId}", 'PUT', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Updated Project',
        ]));

        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertSame('Updated Project', $data['data']['name']);
    }

    public function testDescriptionTooLong(): void
    {
        $projectId = $this->createTestProject();

        $request = Request::create("/api/projects/{$projectId}", 'PUT', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'description' => str_repeat('a', 2001),
        ]));

        $response = $this->kernel->handle($request);

        $this->assertSame(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['error']);
    }
}
