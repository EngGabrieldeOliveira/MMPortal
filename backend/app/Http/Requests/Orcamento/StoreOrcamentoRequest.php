<?php

namespace App\Http\Requests\Orcamento;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrcamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['cliente_id' => ['required', 'exists:clientes,id'], 'validade_ate' => ['nullable', 'date'], 'observacoes' => ['nullable', 'string'], 'itens' => ['required', 'array', 'min:1'], 'itens.*.descricao' => ['required', 'string'], 'itens.*.quantidade' => ['required', 'numeric', 'gt:0'], 'itens.*.unidade' => ['nullable', 'string', 'max:20'], 'itens.*.valor_unitario' => ['required', 'numeric', 'min:0'], 'itens.*.especificacoes' => ['nullable', 'array']];
    }
}
