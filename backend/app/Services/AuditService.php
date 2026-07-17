<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Cliente;
use App\Models\Orcamento;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Solicitacao;
use App\Models\SolicitacaoAnexo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditService
{
    private const HIDDEN_FIELDS = ['password', 'remember_token', 'token', 'api_token'];

    public function record(Model $model, string $action, array $oldValues = [], array $newValues = []): void
    {
        $request = app()->runningInConsole() ? null : request();

        AuditLog::create([
            'user_id' => auth('sanctum')->id() ?? $request?->user()?->id,
            'module' => $this->moduleFor($model),
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'record_code' => $model->getAttribute('codigo') ?? $model->getAttribute('numero') ?? $model->getAttribute('nome_original'),
            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'request_id' => $request?->attributes->get('request_id'),
            'occurred_at' => now(),
        ]);
    }

    private function sanitize(array $values): array
    {
        return Arr::except($values, self::HIDDEN_FIELDS);
    }

    private function moduleFor(Model $model): string
    {
        return match ($model::class) {
            Cliente::class => 'comercial.clientes',
            Solicitacao::class => 'comercial.solicitacoes',
            Orcamento::class => 'comercial.orcamentos',
            Pedido::class => 'comercial.pedidos',
            OrdemServico::class => 'operacao.ordens_servico',
            SolicitacaoAnexo::class => 'comercial.solicitacoes.anexos',
            default => 'sistema',
        };
    }
}
