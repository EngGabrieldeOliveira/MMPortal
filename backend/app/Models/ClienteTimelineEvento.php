<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class ClienteTimelineEvento extends Model
{
    protected $table = 'cliente_timeline_eventos';

    protected $fillable = ['uuid', 'cliente_id', 'tipo', 'titulo', 'descricao', 'origem_type', 'origem_id', 'usuario_id', 'ocorreu_em'];

    protected function casts(): array
    {
        return ['ocorreu_em' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function origem(): MorphTo
    {
        return $this->morphTo();
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
