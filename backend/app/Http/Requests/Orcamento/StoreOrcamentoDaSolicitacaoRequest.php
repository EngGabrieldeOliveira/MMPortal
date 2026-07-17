<?php

namespace App\Http\Requests\Orcamento;

class StoreOrcamentoDaSolicitacaoRequest extends StoreOrcamentoRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['cliente_id']);

        return $rules;
    }
}
