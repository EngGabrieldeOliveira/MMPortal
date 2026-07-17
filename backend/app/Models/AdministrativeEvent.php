<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AdministrativeEvent extends Model
{
    protected $fillable = ['uuid', 'user_id', 'event', 'level', 'module', 'description', 'context', 'ip_address', 'user_agent', 'request_id', 'occurred_at'];

    protected $casts = ['context' => 'array', 'occurred_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $event) => $event->uuid ??= (string) Str::uuid());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
