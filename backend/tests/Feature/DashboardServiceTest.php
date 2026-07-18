<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Orcamento;
use App\Models\OrdemServico;
use App\Models\OrdemServicoEtapa;
use App\Models\Pedido;
use App\Models\Solicitacao;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_real_cockpit_data_from_operational_records(): void
    {
        $today = CarbonImmutable::today();
        $client = Cliente::create(['codigo' => 'CLI-000001', 'tipo' => 'juridica', 'nome' => 'Cliente Dashboard', 'email' => 'cliente@teste.local', 'telefone' => '51999999999', 'empresa' => 'Empresa Dashboard', 'cidade' => 'Santa Cruz do Sul', 'estado' => 'RS', 'status' => 'ativo']);
        $request = Solicitacao::create(['codigo' => 'SOL-000001', 'cliente_id' => $client->id, 'nome_contato' => 'Contato', 'origem' => 'email', 'status' => 'em_analise', 'descricao' => 'Demanda para o dashboard', 'proxima_acao_em' => $today->subDay()]);
        Orcamento::create(['cliente_id' => $client->id, 'solicitacao_id' => $request->id, 'numero' => 'ORC-000001', 'status' => 'enviado', 'valor_total' => 1500, 'enviado_em' => $today->subDay()]);
        $order = Pedido::create(['numero' => 'PED-000001', 'cliente_id' => $client->id, 'valor_total' => 2500, 'quantidade_itens' => 1, 'status' => 'em_producao', 'data_pedido' => $today, 'data_entrega_prevista' => $today->addDay()]);
        $serviceOrder = OrdemServico::create(['pedido_id' => $order->id, 'numero' => 'OS-000001', 'titulo' => 'Estrutura Dashboard', 'status' => 'em_producao', 'prazo_planejado' => $today->subDay()]);
        OrdemServicoEtapa::create(['ordem_servico_id' => $serviceOrder->id, 'tipo' => 'fabricacao', 'sequencia' => 1, 'status' => 'concluida', 'concluida_em' => $today]);

        $data = app(DashboardService::class)->overview();

        $this->assertSame('1', collect($data['kpis'])->firstWhere('id', 'works')['value']);
        $this->assertSame('1', collect($data['kpis'])->firstWhere('id', 'quotes')['value']);
        $this->assertSame('1', collect($data['kpis'])->firstWhere('id', 'orders')['value']);
        $this->assertSame('1', collect($data['kpis'])->firstWhere('id', 'clients')['value']);
        $this->assertNotEmpty($data['attention_items']);
        $this->assertCount(7, $data['production']);
        $this->assertCount(7, $data['revenue']);
        $this->assertSame('PED-000001', $data['works'][0]['id']);
        $this->assertNotEmpty($data['movements']);
    }

    public function test_dashboard_endpoint_requires_authentication_and_returns_standard_envelope(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonStructure(['data' => ['kpis', 'attention_items', 'production', 'revenue', 'works', 'movements'], 'message', 'errors']);
    }
}
