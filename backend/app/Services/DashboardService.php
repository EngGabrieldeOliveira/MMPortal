<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Orcamento;
use App\Models\OrdemServico;
use App\Models\OrdemServicoEtapa;
use App\Models\Pedido;
use App\Models\Solicitacao;
use Carbon\CarbonImmutable;
use DateTimeInterface;

class DashboardService
{
    private const FINISHED_WORK_STATUSES = ['concluido', 'entregue', 'cancelado'];

    private const CLOSED_OS_STATUSES = ['concluida', 'cancelada'];

    /** @return array<string, mixed> */
    public function overview(): array
    {
        $today = CarbonImmutable::today();

        return [
            'kpis' => $this->kpis($today),
            'attention_items' => $this->attentionItems($today),
            'production' => $this->weeklyProduction($today),
            'revenue' => $this->monthlyRevenue($today),
            'works' => $this->works($today),
            'movements' => $this->recentMovements(),
        ];
    }

    /** @return array<int, array<string, string>> */
    private function kpis(CarbonImmutable $today): array
    {
        $worksInProgress = Pedido::query()->whereNotIn('status', self::FINISHED_WORK_STATUSES)->count();
        $worksCompleted = Pedido::query()->whereIn('status', ['concluido', 'entregue'])->count();
        $quotesWaiting = Orcamento::query()->where('status', 'enviado')->count();
        $openOrders = OrdemServico::query()->whereNotIn('status', self::CLOSED_OS_STATUSES)->count();
        $clientsWaiting = Solicitacao::query()
            ->whereNotIn('status', ['convertida', 'encerrada'])
            ->whereNotNull('proxima_acao_em')
            ->where('proxima_acao_em', '<=', $today->endOfDay())
            ->count();
        $forecastRevenue = Pedido::query()
            ->whereNotIn('status', ['cancelado'])
            ->whereBetween('data_entrega_prevista', [$today->startOfMonth(), $today->endOfMonth()])
            ->sum('valor_total');

        return [
            $this->kpi('works', 'Obras em andamento', (string) $worksInProgress, 'Pedidos aprovados em execução', 'orange'),
            $this->kpi('completed-works', 'Obras concluídas', (string) $worksCompleted, 'Obras finalizadas no histórico', 'green'),
            $this->kpi('quotes', 'Orçamentos aguardando', (string) $quotesWaiting, 'Aguardando decisão do cliente', 'yellow'),
            $this->kpi('orders', 'OS abertas', (string) $openOrders, 'Em planejamento, fabricação ou instalação', 'blue'),
            // O módulo de Compras ainda não possui entidade própria. O indicador é real e será preenchido assim que a fonte existir.
            $this->kpi('purchases', 'Compras pendentes', '0', 'Módulo de compras ainda não implantado', 'violet'),
            $this->kpi('clients', 'Clientes aguardando retorno', (string) $clientsWaiting, 'Solicitações com ação prevista para hoje ou antes', 'green'),
            $this->kpi('revenue', 'Faturamento previsto', $this->currency((float) $forecastRevenue), 'Previsão de entrega para o mês atual', 'emerald'),
        ];
    }

    /** @return array<string, string> */
    private function kpi(string $id, string $label, string $value, string $description, string $tone): array
    {
        return compact('id', 'label', 'value', 'description', 'tone');
    }

    /** @return array<int, array<string, string>> */
    private function attentionItems(CarbonImmutable $today): array
    {
        $items = collect();

        OrdemServico::query()
            ->whereNotIn('status', self::CLOSED_OS_STATUSES)
            ->whereNotNull('prazo_planejado')
            ->whereDate('prazo_planejado', '<', $today)
            ->orderBy('prazo_planejado')
            ->limit(5)
            ->get()
            ->each(fn (OrdemServico $order) => $items->push($this->attention('critical', "{$order->numero} está atrasada", $order->titulo, '/obras', $order->prazo_planejado?->toDateString() ?? $today->toDateString())));

        Orcamento::query()
            ->where('status', 'enviado')
            ->orderBy('enviado_em')
            ->limit(5)
            ->get()
            ->each(fn (Orcamento $quote) => $items->push($this->attention('attention', "{$quote->numero} aguarda aprovação", 'Proposta enviada ao cliente e ainda sem decisão.', '/comercial/orcamentos', ($quote->enviado_em ?? $quote->created_at)->toDateString())));

        Solicitacao::query()
            ->whereNotIn('status', ['convertida', 'encerrada'])
            ->whereNotNull('proxima_acao_em')
            ->where('proxima_acao_em', '<=', $today->endOfDay())
            ->orderBy('proxima_acao_em')
            ->limit(5)
            ->get()
            ->each(fn (Solicitacao $request) => $items->push($this->attention('attention', "{$request->codigo} aguarda retorno", $request->nome_contato.' precisa de acompanhamento comercial.', '/comercial/solicitacoes/'.$request->id, $request->proxima_acao_em?->toDateString() ?? $today->toDateString())));

        Solicitacao::query()
            ->whereNotIn('status', ['convertida', 'encerrada'])
            ->whereNotNull('prazo_desejado')
            ->whereBetween('prazo_desejado', [$today, $today->addDay()])
            ->orderBy('prazo_desejado')
            ->limit(3)
            ->get()
            ->each(fn (Solicitacao $request) => $items->push($this->attention('normal', "{$request->codigo} tem prazo próximo", 'Demanda prevista para '.$request->prazo_desejado?->format('d/m/Y').'.', '/comercial/solicitacoes/'.$request->id, $request->prazo_desejado?->toDateString() ?? $today->toDateString())));

        return $items
            ->sortBy(fn (array $item) => ['critical' => 0, 'attention' => 1, 'normal' => 2][$item['priority']])
            ->values()
            ->all();
    }

    /** @return array<string, string> */
    private function attention(string $priority, string $title, string $description, string $href, string $date): array
    {
        return ['id' => md5($priority.$title.$href.$date), ...compact('priority', 'title', 'description', 'href', 'date')];
    }

    /** @return array<int, array<string, int|string>> */
    private function weeklyProduction(CarbonImmutable $today): array
    {
        $start = $today->startOfWeek();
        $end = $today->endOfWeek();
        $completed = OrdemServicoEtapa::query()
            ->where('tipo', 'fabricacao')
            ->where('status', 'concluida')
            ->whereBetween('concluida_em', [$start, $end])
            ->get(['concluida_em'])
            ->countBy(fn (OrdemServicoEtapa $stage) => $stage->concluida_em?->dayOfWeekIso);

        return collect(range(0, 6))->map(function (int $offset) use ($start, $completed): array {
            $date = $start->addDays($offset);

            return ['label' => $date->translatedFormat('D'), 'value' => (int) ($completed[$date->dayOfWeekIso] ?? 0)];
        })->all();
    }

    /** @return array<int, array<string, int|string>> */
    private function monthlyRevenue(CarbonImmutable $today): array
    {
        return collect(range(6, 0))->map(function (int $offset) use ($today): array {
            $month = $today->subMonths($offset)->startOfMonth();
            $value = Pedido::query()
                ->whereNotIn('status', ['cancelado'])
                ->whereBetween('data_pedido', [$month, $month->endOfMonth()])
                ->sum('valor_total');

            return ['label' => ucfirst($month->translatedFormat('M')), 'value' => (int) round((float) $value)];
        })->all();
    }

    /** @return array<int, array<string, int|string>> */
    private function works(CarbonImmutable $today): array
    {
        return Pedido::query()
            ->with(['cliente:id,nome,empresa', 'ordensServico:id,pedido_id,status,titulo'])
            ->whereNotIn('status', self::FINISHED_WORK_STATUSES)
            ->latest()
            ->limit(6)
            ->get()
            ->map(function (Pedido $order) use ($today): array {
                $due = $order->data_entrega_prevista;
                $status = $due && $due->lt($today) ? 'late' : ($due && $due->lte($today->addDays(2)) ? 'attention' : 'on-track');
                $serviceOrder = $order->ordensServico->first();

                return [
                    'id' => $order->numero,
                    'name' => $serviceOrder?->titulo ?? "Obra {$order->numero}",
                    'client' => $order->cliente?->empresa ?: $order->cliente?->nome ?: 'Cliente não informado',
                    'stage' => $serviceOrder?->status ? str_replace('_', ' ', $serviceOrder->status) : str_replace('_', ' ', $order->status),
                    'progress' => $this->workProgress($order->status),
                    'due_label' => $due ? $due->format('d/m/Y') : 'Prazo não informado',
                    'status' => $status,
                    'href' => '/obras',
                ];
            })
            ->all();
    }

    private function workProgress(string $status): int
    {
        return match ($status) {
            'aguardando_pcp', 'em_planejamento' => 20,
            'em_producao' => 55,
            'aguardando_instalacao' => 78,
            default => 35,
        };
    }

    /** @return array<int, array<string, string>> */
    private function recentMovements(): array
    {
        return collect([
            ...Cliente::query()->latest()->limit(5)->get()->map(fn (Cliente $client) => $this->movement('client', 'Cliente cadastrado', $client->codigo.' · '.$client->nome, $client->created_at)),
            ...Solicitacao::query()->latest()->limit(5)->get()->map(fn (Solicitacao $request) => $this->movement('request', 'Solicitação registrada', $request->codigo.' · '.$request->nome_contato, $request->created_at)),
            ...Orcamento::query()->latest()->limit(5)->get()->map(fn (Orcamento $quote) => $this->movement('quote', 'Orçamento atualizado', $quote->numero.' · '.str_replace('_', ' ', $quote->status), $quote->updated_at)),
            ...Pedido::query()->latest()->limit(5)->get()->map(fn (Pedido $order) => $this->movement('order', 'Pedido registrado', $order->numero.' · '.str_replace('_', ' ', $order->status), $order->created_at)),
            ...OrdemServico::query()->latest()->limit(5)->get()->map(fn (OrdemServico $order) => $this->movement('work', 'Ordem de serviço atualizada', $order->numero.' · '.$order->titulo, $order->updated_at)),
        ])
            ->sortByDesc('occurred_at')
            ->take(8)
            ->values()
            ->map(function (array $movement): array {
                $movement['time'] = CarbonImmutable::parse($movement['occurred_at'])->diffForHumans();
                unset($movement['occurred_at']);

                return $movement;
            })
            ->all();
    }

    /** @return array<string, string> */
    private function movement(string $type, string $title, string $description, DateTimeInterface|string|null $occurredAt): array
    {
        $occurredAt = CarbonImmutable::parse($occurredAt ?? now())->toISOString();

        return ['id' => md5($type.$title.$description.$occurredAt), 'type' => $type, 'title' => $title, 'description' => $description, 'occurred_at' => $occurredAt];
    }

    private function currency(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
