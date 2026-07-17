<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdemServicoEtapa extends Model
{
    protected $table = 'ordem_servico_etapas';
    protected $fillable = ['ordem_servico_id', 'tipo', 'sequencia', 'status', 'planejada_inicio_em', 'planejada_fim_em', 'iniciada_em', 'concluida_em', 'observacoes'];
    protected $casts = ['planejada_inicio_em' => 'datetime', 'planejada_fim_em' => 'datetime', 'iniciada_em' => 'datetime', 'concluida_em' => 'datetime'];
    public function ordemServico(): BelongsTo { return $this->belongsTo(OrdemServico::class); }
}
