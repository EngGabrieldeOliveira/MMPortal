<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClienteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_list_clients(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $payload = ['tipo' => 'juridica', 'nome' => 'Contato MM', 'email' => 'contato@mmportal.test', 'telefone' => '51999999999', 'empresa' => 'MM Portal', 'cidade' => 'Santa Cruz do Sul', 'estado' => 'RS', 'documento' => '12345678000190'];

        $this->postJson('/api/comercial/clientes', $payload)->assertCreated()->assertJsonPath('data.nome', 'Contato MM')->assertJsonPath('data.codigo', 'CLI-000001');
        $this->getJson('/api/comercial/clientes')->assertOk()->assertJsonCount(1, 'data.data')->assertJsonPath('data.data.0.email', 'contato@mmportal.test');
    }
}
