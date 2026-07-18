<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = ['uuid', 'codigo', 'tipo', 'nome', 'email', 'documento', 'telefone', 'empresa', 'cidade', 'estado', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'status', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'inscricao_estadual', 'inscricao_municipal', 'segmento', 'condicao_pagamento_padrao', 'limite_faturamento', 'sem_limite_faturamento', 'ativado_em', 'ultimo_pedido_em', 'observacoes_internas', 'matriz_id', 'responsavel_id'];

    protected function casts(): array
    {
        return ['limite_faturamento' => 'decimal:2', 'sem_limite_faturamento' => 'boolean', 'ativado_em' => 'datetime', 'ultimo_pedido_em' => 'datetime'];
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function matriz(): BelongsTo
    {
        return $this->belongsTo(self::class, 'matriz_id');
    }

    public function filiais(): HasMany
    {
        return $this->hasMany(self::class, 'matriz_id');
    }

    public function classificacoes(): BelongsToMany
    {
        return $this->belongsToMany(Classificacao::class, 'cliente_classificacoes')->withTimestamps();
    }

    public function contatos(): HasMany
    {
        return $this->hasMany(ClienteContato::class);
    }

    public function enderecoCobranca(): HasOne
    {
        return $this->hasOne(ClienteEndereco::class)->where('tipo', 'cobranca');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ClienteDocumento::class);
    }

    public function responsaveis(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cliente_responsaveis')->withPivot('papel')->withTimestamps();
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class);
    }

    public function timelineEventos(): HasMany
    {
        return $this->hasMany(ClienteTimelineEvento::class);
    }
}
