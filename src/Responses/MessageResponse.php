<?php

declare(strict_types=1);

namespace App\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MessageResponse
{
    public function __construct(
        private readonly string $message,
    ) {
    }

    public function toJsonResponse(int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['message' => $this->message], $statusCode);
    }
}
