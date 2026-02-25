<?php

declare(strict_types=1);

namespace App\Responses;

use App\Models\PaginatedResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class PaginatedResponse
{
    /**
     * @param string[] $groups
     */
    public static function fromResult(
        PaginatedResult $result,
        SerializerInterface $serializer,
        array $groups = ['read'],
    ): JsonResponse {
        return JsonResponse::fromJsonString(
            $serializer->serialize([
                'data' => $result->items,
                'meta' => [
                    'current_page' => $result->page,
                    'per_page' => $result->perPage,
                    'total' => $result->total,
                    'last_page' => $result->lastPage(),
                ],
            ], 'json', ['groups' => $groups])
        );
    }
}
