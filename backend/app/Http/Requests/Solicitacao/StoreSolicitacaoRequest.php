<?php

namespace App\Http\Requests\Solicitacao;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['cliente_id' => ['nullable', 'exists:clientes,id'], 'nome_contato' => ['required', 'string', 'max:255'], 'email_contato' => ['nullable', 'email'], 'telefone_contato' => ['nullable', 'string', 'max:30'], 'origem' => ['required', Rule::in(['whatsapp', 'email', 'telefone', 'indicacao', 'site', 'visita', 'outro'])], 'descricao' => ['required', 'string'], 'prazo_desejado' => ['nullable', 'date'], 'proxima_acao_em' => ['nullable', 'date'], 'responsavel_id' => ['nullable', 'exists:users,id']];
    }
}
