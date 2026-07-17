<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Permission;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CorporateInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_changes_are_audited_and_soft_deleted(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $payload = ['tipo' => 'juridica', 'nome' => 'Cliente Auditável', 'email' => 'auditavel@mmportal.test', 'telefone' => '51999999999', 'empresa' => 'MM Portal', 'cidade' => 'Santa Cruz do Sul', 'estado' => 'RS'];

        $clientId = $this->postJson('/api/comercial/clientes', $payload)->assertCreated()->json('data.id');
        $this->patchJson("/api/comercial/clientes/{$clientId}", ['telefone' => '51988888888'])->assertOk();
        $this->deleteJson("/api/comercial/clientes/{$clientId}")->assertOk();

        $this->assertSoftDeleted('clientes', ['id' => $clientId]);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $user->id, 'auditable_type' => Cliente::class, 'auditable_id' => $clientId, 'action' => 'created']);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $clientId, 'action' => 'updated']);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $clientId, 'action' => 'soft_deleted']);
    }

    public function test_role_and_individual_permissions_support_overrides(): void
    {
        $user = User::factory()->create(['role' => 'comercial']);
        $permission = Permission::create(['key' => 'clientes.manage', 'module' => 'comercial', 'name' => 'Gerenciar clientes']);
        app(PermissionService::class)->setRolePermission('comercial', $permission);

        $this->assertTrue($user->hasPermission('clientes.manage'));
        $user->permissions()->attach($permission->id, ['allowed' => false]);
        $this->assertFalse($user->hasPermission('clientes.manage'));
        $this->assertDatabaseHas('administrative_events', ['event' => 'security.permission_updated']);
    }

    public function test_authentication_events_are_persisted(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $token = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password'])->assertOk()->json('data.token');
        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'invalid'])->assertUnprocessable();
        $this->withToken($token)->postJson('/api/auth/logout')->assertOk();

        $this->assertDatabaseHas('administrative_events', ['event' => 'auth.login', 'user_id' => $user->id]);
        $this->assertDatabaseHas('administrative_events', ['event' => 'auth.login_failed', 'level' => 'warning']);
        $this->assertDatabaseHas('administrative_events', ['event' => 'auth.logout']);
    }

    public function test_api_exceptions_keep_the_standard_json_contract(): void
    {
        $this->getJson('/api/recurso-inexistente')->assertNotFound()->assertJsonPath('data', null)->assertJsonStructure(['message', 'errors']);
    }
}
