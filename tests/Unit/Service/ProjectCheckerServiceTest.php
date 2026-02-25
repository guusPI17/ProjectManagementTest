<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Models\ProjectCheck;
use App\Repositories\ProjectCheckRepository;
use App\Services\ProjectCheckerService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ProjectCheckerServiceTest extends TestCase
{
    public function testCheckReturnsAvailableForSuccessfulResponse(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $savedCheck = null;
        $checkRepo = $this->createMock(ProjectCheckRepository::class);
        $checkRepo->method('create')
            ->willReturnCallback(function (ProjectCheck $check) use (&$savedCheck) {
                $savedCheck = $check;
                $check->id = 1;
                $check->checkedAt = '2026-02-25 12:00:00';

                return $check;
            });

        $service = new ProjectCheckerService($httpClient, $checkRepo);
        $result = $service->check(1, 'https://example.com');

        $this->assertSame(200, $result->httpStatusCode);
        $this->assertTrue($result->isAvailable);
        $this->assertSame(1, $result->projectId);
        $this->assertNull($result->message);
    }

    public function testCheckReturnsUnavailableForServerError(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $checkRepo = $this->createMock(ProjectCheckRepository::class);
        $checkRepo->method('create')
            ->willReturnCallback(function (ProjectCheck $check) {
                $check->id = 1;
                $check->checkedAt = '2026-02-25 12:00:00';

                return $check;
            });

        $service = new ProjectCheckerService($httpClient, $checkRepo);
        $result = $service->check(1, 'https://example.com');

        $this->assertSame(500, $result->httpStatusCode);
        $this->assertFalse($result->isAvailable);
    }

    public function testCheckReturnsUnavailableOnException(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')
            ->willThrowException(new \RuntimeException('Connection timeout'));

        $checkRepo = $this->createMock(ProjectCheckRepository::class);
        $checkRepo->method('create')
            ->willReturnCallback(function (ProjectCheck $check) {
                $check->id = 1;
                $check->checkedAt = '2026-02-25 12:00:00';

                return $check;
            });

        $service = new ProjectCheckerService($httpClient, $checkRepo);
        $result = $service->check(1, 'https://example.com');

        $this->assertNull($result->httpStatusCode);
        $this->assertFalse($result->isAvailable);
        $this->assertSame('Connection timeout', $result->message);
    }

    public function testCheckMeasuresResponseTime(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $checkRepo = $this->createMock(ProjectCheckRepository::class);
        $checkRepo->method('create')
            ->willReturnCallback(function (ProjectCheck $check) {
                $check->id = 1;
                $check->checkedAt = '2026-02-25 12:00:00';

                return $check;
            });

        $service = new ProjectCheckerService($httpClient, $checkRepo);
        $result = $service->check(1, 'https://example.com');

        $this->assertGreaterThanOrEqual(0, $result->responseTimeMs);
    }
}
