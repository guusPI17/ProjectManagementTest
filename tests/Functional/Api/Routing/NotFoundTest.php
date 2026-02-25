<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api\Routing;

use App\Tests\Support\AppTestCase;
use Symfony\Component\HttpFoundation\Request;

class NotFoundTest extends AppTestCase
{
    public function testNotFoundRoute(): void
    {
        $request = Request::create('/api/unknown', 'GET');
        $response = $this->kernel->handle($request);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testMethodNotAllowed(): void
    {
        $request = Request::create('/api/projects', 'PATCH');
        $response = $this->kernel->handle($request);

        $this->assertSame(405, $response->getStatusCode());
    }
}
