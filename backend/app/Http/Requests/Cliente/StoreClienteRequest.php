<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['fisica', 'juridica'])], 'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clientes', 'email')], 'documento' => ['nullable', 'string', 'max:18', Rule::unique('clientes', 'documento')],
            'telefone' => ['required', 'string', 'max:30'], 'empresa' => ['required', 'string', 'max:255'], 'cidade' => ['required', 'string', 'max:255'], 'estado' => ['required', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:9'], 'logradouro' => ['nullable', 'string', 'max:255'], 'numero' => ['nullable', 'string', 'max:20'], 'complemento' => ['nullable', 'string', 'max:255'], 'bairro' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['ativo', 'inativo', 'suspenso'])],
        ];
    }
}
