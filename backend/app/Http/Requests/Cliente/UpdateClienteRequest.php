<?php

namespace App\Http\Requests\Cliente;

use App\Models\Cliente;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class UpdateClienteRequest extends StoreClienteRequest
{
    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $id = $cliente instanceof Cliente ? $cliente->id : $cliente;
        $rules = parent::rules();
        foreach (['razao_social', 'nome_fantasia', 'cpf_cnpj', 'nome', 'empresa'] as $field) {
            $rules[$field] = ['sometimes', ...array_values(array_filter($rules[$field], fn ($rule) => ! $rule instanceof RequiredIf && (! is_string($rule) || (! str_starts_with($rule, 'required_without') && ! str_starts_with($rule, 'required_with')))))];
        }
        $rules['cpf_cnpj'][count($rules['cpf_cnpj']) - 1] = Rule::unique('clientes', 'cpf_cnpj')->ignore($id);
        $rules['documento'][count($rules['documento']) - 1] = Rule::unique('clientes', 'documento')->ignore($id);
        $rules['email'][count($rules['email']) - 1] = Rule::unique('clientes', 'email')->ignore($id);

        return $rules;
    }
}
