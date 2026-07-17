<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'numero', 'cliente_id', 'valor_total', 'quantidade_itens', 'status', 'data_pedido',
        'data_entrega_prevista', 'data_entrega_real', 'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'float',
        'data_pedido' => 'date:Y-m-d',
        'data_entrega_prevista' => 'date:Y-m-d',
        'data_entrega_real' => 'date:Y-m-d',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
