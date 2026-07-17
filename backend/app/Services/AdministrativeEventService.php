<?php

namespace App\Services;

use App\Enums\AdministrativeEventType;
use App\Models\AdministrativeEvent;
use Illuminate\Support\Facades\Log;

class AdministrativeEventService
{
    public function record(AdministrativeEventType|string $event, string $level = 'info', ?string $module = null, ?string $description = null, array $context = [], ?int $userId = null): void
    {
        $eventName = $event instanceof AdministrativeEventType ? $event->value : $event;
        $request = app()->runningInConsole() ? null : request();
        $payload = ['event' => $eventName, 'level' => $level, 'module' => $module, 'description' => $description, 'context' => $context, 'user_id' => $userId ?? auth('sanctum')->id() ?? $request?->user()?->id, 'ip_address' => $request?->ip(), 'user_agent' => $request?->userAgent(), 'request_id' => $request?->attributes->get('request_id'), 'occurred_at' => now()];

        AdministrativeEvent::create($payload);
        Log::channel('administrative')->{$level}($eventName, $payload);
    }
}
