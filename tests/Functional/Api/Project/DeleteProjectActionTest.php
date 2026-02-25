<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Project;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class DeleteProjectActionTest extends AppTestCase
{
    public function testReturnsSuccess(): void
    {
        $projectId = $this->createTestProject();

        $request = Request::create("/api/projects/{$projectId}", 'DELETE');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $data);
    }

    public function testNotFound(): void
    {
        $request = Request::create('/api/projects/99999', 'DELETE');
        $response = $this->kernel->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }
}
