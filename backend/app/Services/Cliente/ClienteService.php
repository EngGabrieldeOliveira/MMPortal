<?php

namespace App\Services\Cliente;

use App\Models\Classificacao;
use App\Models\Cliente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClienteService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $query = Cliente::query()->with(['classificacoes', 'enderecoCobranca', 'contatos', 'responsavel'])->withCount('pedidos');
        $query->when($search, fn ($q) => $q->where(fn ($nested) => $nested->where('nome', 'like', "%{$search}%")->orWhere('razao_social', 'like', "%{$search}%")->orWhere('nome_fantasia', 'like', "%{$search}%")->orWhere('cpf_cnpj', 'like', "%{$search}%")->orWhere('documento', 'like', "%{$search}%")->orWhereHas('contatos', fn ($contacts) => $contacts->where('nome', 'like', "%{$search}%")->orWhere('telefone', 'like', "%{$search}%")->orWhere('whatsapp', 'like', "%{$search}%"))));
        foreach (['nome' => 'razao_social', 'fantasia' => 'nome_fantasia', 'cnpj' => 'cpf_cnpj', 'status' => 'status'] as $filter => $column) {
            $query->when($filters[$filter] ?? null, fn ($q, $value) => $q->where($column, 'like', "%{$value}%"));
        }
        $query->when($filters['cidade'] ?? null, fn ($q, $value) => $q->whereHas('enderecoCobranca', fn ($address) => $address->where('cidade', 'like', "%{$value}%")));
        $query->when($filters['contato'] ?? null, fn ($q, $value) => $q->whereHas('contatos', fn ($contact) => $contact->where('nome', 'like', "%{$value}%")));
        $query->when($filters['telefone'] ?? null, fn ($q, $value) => $q->whereHas('contatos', fn ($contact) => $contact->where('telefone', 'like', "%{$value}%")->orWhere('whatsapp', 'like', "%{$value}%")));
        $query->when($filters['classificacao'] ?? null, fn ($q, $value) => $q->whereHas('classificacoes', fn ($classes) => $classes->where('chave', $value)));
        $sort = in_array($filters['sort'] ?? '', ['codigo', 'razao_social', 'nome_fantasia', 'status', 'created_at'], true) ? $filters['sort'] : 'created_at';

        return $query->orderBy($sort, ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc')->paginate(min(max((int) ($filters['limit'] ?? 15), 1), 100));
    }

    public function create(array $data): Cliente
    {
        return DB::transaction(fn () => $this->persist(new Cliente, $data));
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        return DB::transaction(fn () => $this->persist($cliente, $data));
    }

    public function delete(Cliente $cliente): void
    {
        $cliente->delete();
    }

    private function persist(Cliente $cliente, array $data): Cliente
    {
        if (! array_key_exists('responsavel_id', $data) && isset($data['responsaveis'][0]['user_id'])) {
            $data['responsavel_id'] = $data['responsaveis'][0]['user_id'];
        }
        $base = Arr::only($data, ['tipo', 'nome', 'email', 'documento', 'telefone', 'empresa', 'cidade', 'estado', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'status', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'inscricao_estadual', 'inscricao_municipal', 'segmento', 'condicao_pagamento_padrao', 'limite_faturamento', 'sem_limite_faturamento', 'observacoes_internas', 'matriz_id', 'responsavel_id']);
        $base['tipo'] = $data['tipo_pessoa'] ?? $data['tipo'] ?? $cliente->tipo ?? 'juridica';
        $base['razao_social'] = $data['razao_social'] ?? $data['nome'] ?? $cliente->razao_social;
        $base['nome_fantasia'] = $data['nome_fantasia'] ?? $data['empresa'] ?? $cliente->nome_fantasia;
        $base['cpf_cnpj'] = $data['cpf_cnpj'] ?? $data['documento'] ?? $cliente->cpf_cnpj;
        $base['nome'] = $data['nome'] ?? $base['razao_social'] ?? $cliente->nome;
        $base['empresa'] = $data['empresa'] ?? $base['nome_fantasia'] ?? $cliente->empresa;
        $base['documento'] = $data['documento'] ?? $base['cpf_cnpj'] ?? $cliente->documento;
        if ($base['tipo'] === 'fisica') {
            $base['razao_social'] = $data['razao_social'] ?? $data['nome'] ?? $cliente->razao_social;
            $base['nome_fantasia'] = null;
            $base['empresa'] = $base['razao_social'];
            $base['inscricao_estadual'] = null;
            $base['inscricao_municipal'] = null;
        }
        if (($data['sem_limite_faturamento'] ?? false) === true) {
            $base['limite_faturamento'] = null;
        }
        $primaryContact = collect($data['contatos'] ?? [])->firstWhere('is_principal', true) ?? ($data['contatos'][0] ?? []);
        $address = $data['endereco_cobranca'] ?? [];
        $base['email'] = $data['email'] ?? $primaryContact['email'] ?? $cliente->email;
        $base['telefone'] = $data['telefone'] ?? $primaryContact['telefone'] ?? $primaryContact['whatsapp'] ?? $cliente->telefone;
        $base['cidade'] = $data['cidade'] ?? $address['cidade'] ?? $cliente->cidade;
        $base['estado'] = $data['estado'] ?? $address['estado'] ?? $cliente->estado;
        if (! $cliente->exists) {
            $base += ['uuid' => (string) Str::uuid(), 'codigo' => $this->nextCode()];
        }
        if (($base['status'] ?? $cliente->status) === 'ativo' && ! $cliente->ativado_em) {
            $base['ativado_em'] = now();
        }
        $cliente->fill($base)->save();

        if (array_key_exists('classificacoes', $data)) {
            $cliente->classificacoes()->sync(Classificacao::query()->whereIn('chave', $data['classificacoes'])->pluck('id'));
        }
        if (isset($data['endereco_cobranca'])) {
            $cliente->enderecoCobranca()->updateOrCreate(['tipo' => 'cobranca'], $data['endereco_cobranca'] + ['tipo' => 'cobranca']);
        } elseif (! $cliente->enderecoCobranca && isset($data['cidade'], $data['estado'])) {
            $cliente->enderecoCobranca()->create(Arr::only($data, ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado']) + ['tipo' => 'cobranca', 'cep' => $data['cep'] ?? '', 'logradouro' => $data['logradouro'] ?? 'Não informado']);
        }
        if (isset($data['contatos'])) {
            foreach ($data['contatos'] as $contact) {
                $id = $contact['id'] ?? null;
                unset($contact['id']);
                $model = $id ? $cliente->contatos()->whereKey($id)->firstOrFail() : $cliente->contatos()->make();
                $model->fill($contact);
                $model->cliente_id = $cliente->id;
                $model->save();
            }
        } elseif (! $cliente->contatos()->exists() && ($data['email'] ?? null || $data['telefone'] ?? null)) {
            $cliente->contatos()->create(['nome' => $cliente->nome, 'email' => $data['email'] ?? null, 'telefone' => $data['telefone'] ?? null, 'is_principal' => true]);
        }
        if (array_key_exists('responsavel_id', $data)) {
            $cliente->responsavel_id = $data['responsavel_id'];
            $cliente->save();
        }

        return $cliente->fresh(['classificacoes', 'enderecoCobranca', 'contatos', 'responsavel', 'matriz', 'filiais']);
    }

    private function nextCode(): string
    {
        return 'CLI-'.str_pad((string) ((int) Cliente::withTrashed()->max('id') + 1), 6, '0', STR_PAD_LEFT);
    }
}
