<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_valid_returns_a_sanctum_bearer_token(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $response = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertOk()->assertJsonPath('message', 'Login realizado com sucesso.')->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email', 'role']], 'errors']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_invalid_returns_standard_errors(): void
    {
        User::factory()->create(['password' => 'password']);

        $this->postJson('/api/auth/login', ['email' => 'invalid@example.com', 'password' => 'wrong'])->assertUnprocessable()->assertJsonPath('data', null)->assertJsonStructure(['message', 'errors' => ['email']]);
    }

    public function test_protected_route_requires_a_token(): void
    {
        $this->getJson('/api/comercial/clientes')->assertUnauthorized()->assertJsonPath('message', 'Não autenticado.');
    }

    public function test_sanctum_token_authenticates_the_current_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/auth/me')->assertOk()->assertJsonPath('data.id', $user->id);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = User::factory()->create();
        $current = $user->createToken('current')->plainTextToken;
        $user->createToken('other');

        $this->withToken($current)->postJson('/api/auth/logout')->assertOk()->assertJsonPath('message', 'Sessão encerrada com sucesso.');
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }
}
