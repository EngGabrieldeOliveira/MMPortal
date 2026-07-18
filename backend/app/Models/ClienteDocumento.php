<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClienteDocumento extends Model
{
    use SoftDeletes;

    protected $fillable = ['cliente_id', 'tipo', 'nome_original', 'nome_exibicao', 'caminho', 'mime_type', 'tamanho', 'observacoes', 'enviado_por'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function enviadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enviado_por');
    }
}
