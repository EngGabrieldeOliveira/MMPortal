<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orcamento extends Model
{
    protected $fillable = ['solicitacao_id', 'cliente_id', 'numero', 'versao', 'status', 'valor_total', 'validade_ate', 'enviado_em', 'decidido_em', 'observacoes'];

    protected $casts = ['valor_total' => 'float', 'validade_ate' => 'date:Y-m-d', 'enviado_em' => 'datetime', 'decidido_em' => 'datetime'];

    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(Solicitacao::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(OrcamentoItem::class);
    }
}
