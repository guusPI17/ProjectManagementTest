<?php

declare(strict_types=1);

namespace App\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        private readonly string $message,
        private readonly array $errors = [],
    ) {
    }

    public function toJsonResponse(int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $data = [
            'error' => true,
            'message' => $this->message,
        ];

        if ($this->errors !== []) {
            $data['errors'] = $this->errors;
        }

        return new JsonResponse($data, $statusCode);
    }
}
