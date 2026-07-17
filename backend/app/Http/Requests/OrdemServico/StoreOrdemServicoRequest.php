<?php

namespace App\Http\Requests\OrdemServico;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrdemServicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['titulo' => ['required', 'string', 'max:255'], 'prioridade' => ['required', Rule::in(['baixa', 'normal', 'alta', 'urgente'])], 'prazo_planejado' => ['nullable', 'date'], 'observacoes' => ['nullable', 'string']];
    }
}
