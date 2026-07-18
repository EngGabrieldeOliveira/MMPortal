<?php

namespace App\Http\Requests\Cliente;

use App\Rules\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('cpf_cnpj')) {
            $this->merge(['cpf_cnpj' => preg_replace('/\D/', '', (string) $this->cpf_cnpj)]);
        } if ($this->filled('documento')) {
            $this->merge(['documento' => preg_replace('/\D/', '', (string) $this->documento)]);
        }
    }

    public function rules(): array
    {
        return [
            'tipo_pessoa' => ['sometimes', Rule::in(['fisica', 'juridica'])], 'tipo' => ['sometimes', Rule::in(['fisica', 'juridica'])],
            'razao_social' => [Rule::requiredIf($this->has('tipo_pessoa') && $this->input('tipo_pessoa') === 'juridica'), 'nullable', 'string', 'max:255'], 'nome_fantasia' => [Rule::requiredIf($this->has('tipo_pessoa') && $this->input('tipo_pessoa') === 'juridica'), 'nullable', 'string', 'max:255'],
            'cpf_cnpj' => [Rule::requiredIf($this->has('tipo_pessoa')), 'nullable', 'string', new CpfCnpj, Rule::unique('clientes', 'cpf_cnpj')],
            'nome' => ['required_without:razao_social', 'string', 'max:255'], 'empresa' => [Rule::requiredIf(! $this->has('tipo_pessoa') && ! $this->filled('nome_fantasia')), 'nullable', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:18', Rule::unique('clientes', 'documento')],
            'inscricao_estadual' => ['nullable', 'string', 'max:50'], 'inscricao_municipal' => ['nullable', 'string', 'max:50'], 'segmento' => ['nullable', 'string', 'max:120'],
            'condicao_pagamento_padrao' => ['nullable', 'string', 'max:500'], 'sem_limite_faturamento' => ['sometimes', 'boolean'], 'limite_faturamento' => ['nullable', 'numeric', 'min:0'], 'observacoes_internas' => ['nullable', 'string'], 'matriz_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'status' => ['sometimes', Rule::in(['ativo', 'inativo', 'bloqueado', 'suspenso'])],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('clientes', 'email')], 'telefone' => ['nullable', 'string', 'max:30'], 'cidade' => ['nullable', 'string', 'max:255'], 'estado' => ['nullable', 'string', 'size:2'], 'cep' => ['nullable', 'string', 'max:9'], 'logradouro' => ['nullable', 'string', 'max:255'], 'numero' => ['nullable', 'string', 'max:20'], 'complemento' => ['nullable', 'string', 'max:255'], 'bairro' => ['nullable', 'string', 'max:255'],
            'classificacoes' => ['sometimes', 'array', 'min:1'], 'classificacoes.*' => ['string', Rule::exists('classificacoes', 'chave')],
            'endereco_cobranca' => ['sometimes', 'array'], 'endereco_cobranca.cep' => ['required_with:endereco_cobranca', 'string', 'max:9'], 'endereco_cobranca.logradouro' => ['required_with:endereco_cobranca', 'string', 'max:255'], 'endereco_cobranca.cidade' => ['required_with:endereco_cobranca', 'string', 'max:255'], 'endereco_cobranca.estado' => ['required_with:endereco_cobranca', 'string', 'size:2'], 'endereco_cobranca.numero' => ['nullable', 'string', 'max:20'], 'endereco_cobranca.complemento' => ['nullable', 'string', 'max:255'], 'endereco_cobranca.bairro' => ['nullable', 'string', 'max:255'],
            'contatos' => ['sometimes', 'array', 'min:1'], 'contatos.*.id' => ['sometimes', 'integer'], 'contatos.*.nome' => ['required_with:contatos', 'string', 'max:255'], 'contatos.*.cargo' => ['nullable', 'string', 'max:120'], 'contatos.*.departamento' => ['nullable', 'string', 'max:120'], 'contatos.*.telefone' => ['nullable', 'string', 'max:30'], 'contatos.*.whatsapp' => ['nullable', 'string', 'max:30'], 'contatos.*.email' => ['nullable', 'email', 'max:255'], 'contatos.*.aprova_orcamentos' => ['sometimes', 'boolean'], 'contatos.*.is_principal' => ['sometimes', 'boolean'],
            'responsavel_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
