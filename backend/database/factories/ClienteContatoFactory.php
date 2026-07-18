<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\ClienteContato;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteContatoFactory extends Factory
{
    protected $model = ClienteContato::class;

    public function definition(): array
    {
        return ['cliente_id' => Cliente::factory(), 'nome' => fake()->name(), 'cargo' => 'Comprador', 'departamento' => 'Compras', 'telefone' => '51999999999', 'whatsapp' => '51999999999', 'email' => fake()->unique()->safeEmail(), 'aprova_orcamentos' => false, 'is_principal' => false];
    }
}
