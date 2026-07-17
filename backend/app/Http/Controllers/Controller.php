<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message, 'errors' => (object) []], $status);
    }

    protected function failure(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json(['data' => null, 'message' => $message, 'errors' => $errors ?: (object) []], $status);
    }
}
