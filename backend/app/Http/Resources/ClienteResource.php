<?php

namespace App\Http\Resources;

use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Cliente */
class ClienteResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'uuid' => $this->uuid, 'codigo' => $this->codigo, 'tipo_pessoa' => $this->tipo, 'tipo' => $this->tipo,
            'razao_social' => $this->razao_social ?? $this->nome, 'nome_fantasia' => $this->nome_fantasia, 'cpf_cnpj' => $this->cpf_cnpj ?? $this->documento,
            'nome' => $this->nome, 'empresa' => $this->empresa, 'email' => $this->email, 'telefone' => $this->telefone,
            'inscricao_estadual' => $this->inscricao_estadual, 'inscricao_municipal' => $this->inscricao_municipal, 'segmento' => $this->segmento,
            'condicao_pagamento_padrao' => $this->condicao_pagamento_padrao, 'limite_faturamento' => $this->limite_faturamento, 'sem_limite_faturamento' => $this->sem_limite_faturamento,
            'cliente_ativo_desde' => $this->ativado_em ? Carbon::parse($this->ativado_em)->toISOString() : null,
            'ultimo_pedido_realizado' => $this->ultimo_pedido_em ? Carbon::parse($this->ultimo_pedido_em)->toISOString() : null,
            'observacoes_internas' => $this->observacoes_internas, 'status' => $this->status,
            'responsavel' => $this->whenLoaded('responsavel', fn (): ?array => $this->responsavel instanceof User ? ['id' => $this->responsavel->id, 'name' => $this->responsavel->name] : null),
            'matriz' => $this->whenLoaded('matriz', fn (): ?array => $this->matriz instanceof Cliente ? ['id' => $this->matriz->id, 'codigo' => $this->matriz->codigo, 'nome' => $this->matriz->razao_social ?? $this->matriz->nome] : null),
            'filiais' => $this->whenLoaded('filiais', fn () => $this->filiais->map(fn (Model $branch): array => ['id' => (int) $branch->getAttribute('id'), 'codigo' => $branch->getAttribute('codigo'), 'nome' => $branch->getAttribute('razao_social') ?? $branch->getAttribute('nome')])),
            'classificacoes' => $this->whenLoaded('classificacoes', fn () => $this->classificacoes->map(fn (Model $class): array => ['chave' => (string) $class->getAttribute('chave'), 'nome' => (string) $class->getAttribute('nome')])),
            'endereco_cobranca' => $this->whenLoaded('enderecoCobranca'), 'contatos' => $this->whenLoaded('contatos'),
            'pedidos_count' => $this->when(isset($this->pedidos_count), $this->pedidos_count),
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toISOString() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toISOString() : null,
        ];
    }
}
