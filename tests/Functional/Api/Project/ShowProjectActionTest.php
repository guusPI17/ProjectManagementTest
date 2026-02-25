<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Project;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class ShowProjectActionTest extends AppTestCase
{
    public function testReturnsProject(): void
    {
        $projectId = $this->createTestProject();

        $request = Request::create("/api/projects/{$projectId}", 'GET');
        $response = $this->kernel->handle($request);

        $this->assertSame(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertSame($projectId, $data['data']['id']);
    }

    public function testNotFound(): void
    {
        $request = Request::create('/api/projects/99999', 'GET');
        $response = $this->kernel->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }
}
