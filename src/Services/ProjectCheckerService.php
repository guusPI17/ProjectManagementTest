<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProjectCheck;
use App\Repositories\ProjectCheckRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProjectCheckerService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ProjectCheckRepository $checkRepository,
    ) {
    }

    public function check(int $projectId, string $url): ProjectCheck
    {
        $check = new ProjectCheck();
        $check->projectId = $projectId;

        $startTime = hrtime(true);

        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10,
                'max_redirects' => 5,
            ]);

            $statusCode = $response->getStatusCode();
            $check->httpStatusCode = $statusCode;
            $check->isAvailable = $statusCode >= 200 && $statusCode < 400;
        } catch (\Throwable $e) {
            $check->httpStatusCode = null;
            $check->isAvailable = false;
            $check->message = mb_substr($e->getMessage(), 0, 2000);
        }

        $elapsedMs = (int) ((hrtime(true) - $startTime) / 1_000_000);
        $check->responseTimeMs = $elapsedMs;

        return $this->checkRepository->create($check);
    }
}
