<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdemServico extends Model
{
    protected $table = 'ordens_servico';

    protected $fillable = ['pedido_id', 'numero', 'titulo', 'status', 'prioridade', 'prazo_planejado', 'observacoes', 'responsavel_id'];

    protected $casts = ['prazo_planejado' => 'date:Y-m-d'];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(OrdemServicoEtapa::class);
    }
}
