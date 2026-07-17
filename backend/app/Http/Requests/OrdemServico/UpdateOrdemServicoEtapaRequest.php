<?php

namespace App\Http\Requests\OrdemServico;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrdemServicoEtapaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['aguardando', 'planejada', 'em_andamento', 'concluida', 'bloqueada'])], 'observacoes' => ['nullable', 'string'], 'planejada_inicio_em' => ['nullable', 'date'], 'planejada_fim_em' => ['nullable', 'date']];
    }
}
