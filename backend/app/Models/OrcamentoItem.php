<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrcamentoItem extends Model
{
    protected $table = 'orcamento_itens';

    protected $fillable = ['orcamento_id', 'ordem', 'descricao', 'quantidade', 'unidade', 'valor_unitario', 'valor_total', 'especificacoes'];

    protected $casts = ['quantidade' => 'float', 'valor_unitario' => 'float', 'valor_total' => 'float', 'especificacoes' => 'array'];

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class);
    }
}
