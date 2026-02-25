<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        string $message = 'Ошибка валидации',
        private readonly array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }

    public static function fromViolations(ConstraintViolationListInterface $violations): self
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }

        return new self('Ошибка валидации', $errors);
    }

    /**
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
