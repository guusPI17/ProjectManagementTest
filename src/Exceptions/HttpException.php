<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class HttpException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
