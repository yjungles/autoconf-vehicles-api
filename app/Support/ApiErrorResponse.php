<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiErrorResponse
{
    public static function make(
        int $status,
        string $title,
        string $detail = '',
        array $errors = [],
        string $type = null
    ): JsonResponse {
        return response()->json([
//            'type' => $type,
            'success' => false,
            'status' => $status,
            'title' => $title,
            'detail' => $detail,
            'errors' => $errors ?: [],
        ], $status);
    }
}
