<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        $document = (string) fake()->unique()->numerify('##############');

        return ['uuid' => (string) Str::uuid(), 'codigo' => 'CLI-'.fake()->unique()->numerify('######'), 'tipo' => 'juridica', 'nome' => fake()->company(), 'email' => fake()->unique()->companyEmail(), 'telefone' => fake()->phoneNumber(), 'empresa' => fake()->company(), 'cidade' => fake()->city(), 'estado' => 'RS', 'status' => 'ativo', 'razao_social' => fake()->company(), 'nome_fantasia' => fake()->company(), 'documento' => $document, 'cpf_cnpj' => $document, 'inscricao_estadual' => fake()->numerify('##########'), 'inscricao_municipal' => fake()->numerify('########'), 'segmento' => 'Construção civil', 'condicao_pagamento_padrao' => '30 dias', 'limite_faturamento' => 10000, 'ativado_em' => now()];
    }
}
