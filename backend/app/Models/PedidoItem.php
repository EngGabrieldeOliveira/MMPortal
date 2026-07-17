<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';

    protected $fillable = ['pedido_id', 'orcamento_item_id', 'ordem', 'descricao', 'quantidade', 'unidade', 'valor_unitario', 'valor_total', 'especificacoes'];

    protected $casts = ['quantidade' => 'float', 'valor_unitario' => 'float', 'valor_total' => 'float', 'especificacoes' => 'array'];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }
}
