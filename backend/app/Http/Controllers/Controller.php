<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    use AuthorizesRequests;

    protected function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message, 'errors' => (object) []], $status);
    }

    protected function failure(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json(['data' => null, 'message' => $message, 'errors' => $errors ?: (object) []], $status);
    }
}
