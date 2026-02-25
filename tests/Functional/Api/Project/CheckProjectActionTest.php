<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Project;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class CheckProjectActionTest extends AppTestCase
{
    public function testReturnsCheckResult(): void
    {
        $projectId = $this->createTestProject();

        $request = Request::create("/api/projects/{$projectId}/check", 'POST');
        $response = $this->kernel->handle($request);

        $this->assertSame(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('http_status_code', $data['data']);
        $this->assertArrayHasKey('is_available', $data['data']);
        $this->assertArrayHasKey('response_time_ms', $data['data']);
    }
}
