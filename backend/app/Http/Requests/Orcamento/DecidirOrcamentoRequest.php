<?php

namespace App\Http\Requests\Orcamento;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DecidirOrcamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['decisao' => ['required', Rule::in(['aceito', 'recusado', 'postergado'])], 'motivo' => ['nullable', 'string'], 'postergada_ate' => ['nullable', 'date']];
    }
}
