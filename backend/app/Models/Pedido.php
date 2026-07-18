<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pedidos';

    protected $fillable = [
        'numero', 'cliente_id', 'orcamento_id', 'valor_total', 'quantidade_itens', 'status', 'data_pedido',
        'data_entrega_prevista', 'data_entrega_real', 'prazo_prometido', 'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'float',
        'data_pedido' => 'date:Y-m-d',
        'data_entrega_prevista' => 'date:Y-m-d',
        'data_entrega_real' => 'date:Y-m-d',
    ];

    /** @return BelongsTo<Cliente, $this> */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /** @return HasMany<OrdemServico, $this> */
    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
