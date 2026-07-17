<?php

namespace App\Http\Requests\Cliente;

use App\Models\Cliente;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends StoreClienteRequest
{
    public function rules(): array
    {
        $cliente = $this->route('cliente');
        $id = $cliente instanceof Cliente ? $cliente->id : $cliente;
        $rules = parent::rules();
        foreach (['tipo', 'nome', 'email', 'telefone', 'empresa', 'cidade', 'estado'] as $field) {
            array_unshift($rules[$field], 'sometimes');
        }
        $rules['email'] = ['sometimes', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($id)];
        $rules['documento'] = ['nullable', 'string', 'max:18', Rule::unique('clientes', 'documento')->ignore($id)];

        return $rules;
    }
}
