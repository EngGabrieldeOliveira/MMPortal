<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteContato extends Model
{
    use SoftDeletes;

    protected $fillable = ['cliente_id', 'nome', 'cargo', 'departamento', 'telefone', 'whatsapp', 'email', 'aprova_orcamentos', 'is_principal'];

    protected function casts(): array
    {
        return ['aprova_orcamentos' => 'boolean', 'is_principal' => 'boolean'];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
