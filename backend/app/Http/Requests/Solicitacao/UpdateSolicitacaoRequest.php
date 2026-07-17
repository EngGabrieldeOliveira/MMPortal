<?php

namespace App\Http\Requests\Solicitacao;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSolicitacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status' => ['sometimes', Rule::in(['nova', 'em_analise', 'em_orcamento', 'orcamento_enviado', 'negociacao', 'postergada', 'encerrada'])], 'proxima_acao_em' => ['nullable', 'date'], 'postergada_ate' => ['nullable', 'date'], 'motivo_encerramento' => ['nullable', 'string']];
    }
}
