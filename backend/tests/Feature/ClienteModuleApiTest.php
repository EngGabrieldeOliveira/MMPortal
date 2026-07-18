<?php

namespace Tests\Feature;

use App\Models\Classificacao;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClienteModuleApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingCommercial(): User
    {
        $user = User::factory()->create(['role' => 'comercial']);
        Sanctum::actingAs($user);
        foreach (['cliente' => 'Cliente', 'fornecedor' => 'Fornecedor', 'transportadora' => 'Transportadora'] as $key => $name) {
            Classificacao::create(['chave' => $key, 'nome' => $name]);
        }

        return $user;
    }

    private function payload(User $responsavel): array
    {
        return ['tipo_pessoa' => 'juridica', 'razao_social' => 'Estruturas Metálicas do Sul Ltda', 'nome_fantasia' => 'Estruturas do Sul', 'cpf_cnpj' => '11222333000181', 'inscricao_estadual' => '123456789', 'inscricao_municipal' => '123456', 'segmento' => 'Metalurgia', 'condicao_pagamento_padrao' => '30 dias', 'limite_faturamento' => 50000, 'status' => 'ativo', 'classificacoes' => ['cliente', 'fornecedor'], 'endereco_cobranca' => ['cep' => '96835-120', 'logradouro' => 'Rua das Indústrias', 'numero' => '100', 'bairro' => 'Industrial', 'cidade' => 'Santa Cruz do Sul', 'estado' => 'RS'], 'contatos' => [['nome' => 'Ana Compras', 'cargo' => 'Compradora', 'departamento' => 'Compras', 'telefone' => '51999999999', 'whatsapp' => '51999999999', 'email' => 'ana@estruturas.test', 'aprova_orcamentos' => true, 'is_principal' => true], ['nome' => 'João Obras', 'telefone' => '51988888888', 'email' => 'joao@estruturas.test']], 'responsavel_id' => $responsavel->id, 'observacoes_internas' => 'Cliente estratégico'];
    }

    public function test_it_creates_a_complete_client_with_relations_and_audit(): void
    {
        $user = $this->actingCommercial();
        $response = $this->postJson('/api/comercial/clientes', $this->payload($user))->assertCreated()->assertJsonPath('data.razao_social', 'Estruturas Metálicas do Sul Ltda')->assertJsonCount(2, 'data.classificacoes')->assertJsonCount(2, 'data.contatos');
        $id = $response->json('data.id');
        $this->assertDatabaseHas('clientes', ['id' => $id, 'cpf_cnpj' => '11222333000181']);
        $this->assertDatabaseCount('cliente_contatos', 2);
        $this->assertDatabaseHas('cliente_enderecos', ['cliente_id' => $id, 'tipo' => 'cobranca', 'cidade' => 'Santa Cruz do Sul']);
        $this->assertDatabaseCount('cliente_classificacoes', 2);
        $this->assertDatabaseHas('clientes', ['id' => $id, 'responsavel_id' => $user->id]);
        $this->assertDatabaseHas('audit_logs', ['auditable_type' => Cliente::class, 'auditable_id' => $id, 'action' => 'created']);
    }

    public function test_it_validates_invalid_or_duplicate_cnpj(): void
    {
        $user = $this->actingCommercial();
        $invalid = $this->payload($user);
        $invalid['cpf_cnpj'] = '11111111111111';
        $this->postJson('/api/comercial/clientes', $invalid)->assertUnprocessable()->assertJsonValidationErrors('cpf_cnpj');
        $valid = $this->payload($user);
        $this->postJson('/api/comercial/clientes', $valid)->assertCreated();
        $valid['razao_social'] = 'Outra empresa';
        $valid['nome_fantasia'] = 'Outra';
        $this->postJson('/api/comercial/clientes', $valid)->assertUnprocessable()->assertJsonValidationErrors('cpf_cnpj');
    }

    public function test_it_filters_searches_paginates_and_supports_branch_relationships(): void
    {
        $user = $this->actingCommercial();
        $main = Cliente::factory()->create(['razao_social' => 'Matriz Alfa', 'nome' => 'Matriz Alfa', 'nome_fantasia' => 'Alfa', 'cpf_cnpj' => '11222333000181', 'documento' => '11222333000181']);
        $branch = Cliente::factory()->create(['matriz_id' => $main->id, 'razao_social' => 'Filial Alfa', 'nome' => 'Filial Alfa', 'nome_fantasia' => 'Alfa Filial', 'cpf_cnpj' => '12345678000195', 'documento' => '12345678000195']);
        $main->classificacoes()->attach(Classificacao::where('chave', 'cliente')->value('id'));
        $branch->contatos()->create(['nome' => 'Maria Comercial', 'telefone' => '51977777777']);
        $this->getJson('/api/comercial/clientes?search=Alfa&limit=1')->assertOk()->assertJsonPath('data.per_page', 1)->assertJsonPath('data.total', 2);
        $this->getJson('/api/comercial/clientes?contato=Maria')->assertOk()->assertJsonPath('data.total', 1)->assertJsonPath('data.data.0.id', $branch->id);
        $this->getJson("/api/comercial/clientes/{$main->id}")->assertOk()->assertJsonPath('data.filiais.0.id', $branch->id);
    }

    public function test_it_enforces_permissions_and_soft_deletes_clients(): void
    {
        $forbidden = User::factory()->create(['role' => 'rh']);
        Sanctum::actingAs($forbidden);
        $this->getJson('/api/comercial/clientes')->assertForbidden();
        $user = $this->actingCommercial();
        $client = Cliente::factory()->create();
        $this->deleteJson("/api/comercial/clientes/{$client->id}")->assertOk();
        $this->assertSoftDeleted('clientes', ['id' => $client->id]);
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $client->id, 'action' => 'soft_deleted']);
        $client->restore();
        $this->assertDatabaseHas('audit_logs', ['auditable_id' => $client->id, 'action' => 'restored']);
    }

    public function test_it_supports_unlimited_billing_and_status_transitions(): void
    {
        $user = $this->actingCommercial();
        $payload = $this->payload($user);
        $payload['sem_limite_faturamento'] = true;
        unset($payload['limite_faturamento']);
        $client = $this->postJson('/api/comercial/clientes', $payload)->assertCreated()->assertJsonPath('data.sem_limite_faturamento', true)->json('data');
        $this->assertDatabaseHas('clientes', ['id' => $client['id'], 'sem_limite_faturamento' => true]);
        $this->assertDatabaseMissing('clientes', ['id' => $client['id'], 'limite_faturamento' => 0]);

        $this->patchJson("/api/comercial/clientes/{$client['id']}/status", ['status' => 'inativo'])->assertOk()->assertJsonPath('data.status', 'inativo');
        $this->patchJson("/api/comercial/clientes/{$client['id']}/status", ['status' => 'ativo'])->assertOk()->assertJsonPath('data.status', 'ativo');
    }

    public function test_it_supports_a_physical_person_and_a_single_internal_owner(): void
    {
        $owner = $this->actingCommercial();
        $payload = $this->payload($owner);
        $payload = array_merge($payload, ['tipo_pessoa' => 'fisica', 'razao_social' => 'Maria da Silva', 'nome_fantasia' => null, 'cpf_cnpj' => '52998224725', 'inscricao_estadual' => null, 'inscricao_municipal' => null, 'responsavel_id' => $owner->id]);
        $response = $this->postJson('/api/comercial/clientes', $payload)->assertCreated()->assertJsonPath('data.tipo_pessoa', 'fisica')->assertJsonPath('data.responsavel.name', $owner->name);
        $this->assertDatabaseHas('clientes', ['id' => $response->json('data.id'), 'responsavel_id' => $owner->id, 'inscricao_estadual' => null, 'inscricao_municipal' => null]);
    }

    public function test_it_lists_only_authorized_user_options(): void
    {
        $commercial = $this->actingCommercial();
        User::factory()->create(['role' => 'rh', 'name' => 'Pessoa RH']);
        $this->getJson('/api/comercial/usuarios/opcoes')->assertOk()->assertJsonPath('data.0.id', $commercial->id)->assertJsonMissing(['name' => 'Pessoa RH']);
    }
}
