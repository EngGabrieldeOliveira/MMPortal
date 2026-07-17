<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    protected $fillable = ['uuid', 'user_id', 'module', 'action', 'auditable_type', 'auditable_id', 'record_code', 'old_values', 'new_values', 'ip_address', 'user_agent', 'request_id', 'occurred_at'];

    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'occurred_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $log) => $log->uuid ??= (string) Str::uuid());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
