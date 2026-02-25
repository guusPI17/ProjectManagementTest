<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends HttpException
{
    public function __construct(string $message = 'Ресурс не найден', ?\Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $previous);
    }
}
