<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitacao extends Model
{
    use SoftDeletes;

    protected $table = 'solicitacoes';

    protected $fillable = ['codigo', 'cliente_id', 'nome_contato', 'email_contato', 'telefone_contato', 'origem', 'status', 'descricao', 'prazo_desejado', 'proxima_acao_em', 'postergada_ate', 'motivo_encerramento', 'responsavel_id'];

    protected $casts = ['prazo_desejado' => 'date:Y-m-d', 'proxima_acao_em' => 'datetime', 'postergada_ate' => 'datetime'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function orcamentos(): HasMany
    {
        return $this->hasMany(Orcamento::class);
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(SolicitacaoAnexo::class);
    }
}
