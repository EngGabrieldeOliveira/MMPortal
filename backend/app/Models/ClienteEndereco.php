<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteEndereco extends Model
{
    use SoftDeletes;

    protected $fillable = ['cliente_id', 'tipo', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'pais', 'referencia'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}
