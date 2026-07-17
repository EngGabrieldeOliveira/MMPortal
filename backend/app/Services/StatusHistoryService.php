<?php

namespace App\Services;

use App\Models\HistoricoStatus;

class StatusHistoryService
{
    public function record(object $model, ?string $previous, string $current, ?string $reason = null): void
    {
        HistoricoStatus::create(['historico_type' => $model::class, 'historico_id' => $model->id, 'status_anterior' => $previous, 'status_novo' => $current, 'motivo' => $reason, 'usuario_id' => request()->user()?->id]);
    }
}
