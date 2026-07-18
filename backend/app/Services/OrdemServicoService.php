<?php

namespace App\Services;

use App\Models\OrdemServico;
use App\Models\OrdemServicoEtapa;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;

class OrdemServicoService
{
    public function __construct(private readonly StatusHistoryService $history) {}

    public function create(Pedido $pedido, array $data): OrdemServico
    {
        $ordem = DB::transaction(function () use ($pedido, $data): OrdemServico {
            $ordem = OrdemServico::create($data + ['pedido_id' => $pedido->id, 'numero' => 'OS-'.str_pad((string) ((int) OrdemServico::max('id') + 1), 6, '0', STR_PAD_LEFT)]);
            $ordem->etapas()->createMany([['tipo' => 'fabricacao', 'sequencia' => 1], ['tipo' => 'instalacao', 'sequencia' => 2]]);
            $pedido->update(['status' => 'em_planejamento']);

            return $ordem->load('etapas');
        });
        $this->history->record($ordem, null, 'aguardando_planejamento');

        return $ordem;
    }

    public function updateStage(OrdemServicoEtapa $etapa, array $data): OrdemServicoEtapa
    {
        $ordemServico = $etapa->ordemServico;
        if ($etapa->tipo === 'instalacao' && $data['status'] === 'em_andamento' && $ordemServico instanceof OrdemServico && $ordemServico->etapas()->where('tipo', 'fabricacao')->where('status', '!=', 'concluida')->exists()) {
            abort(422, 'A fabricação deve ser concluída antes de iniciar a instalação.');
        }
        $previous = $etapa->status;
        $etapa->update($data + ($data['status'] === 'em_andamento' ? ['iniciada_em' => now()] : []) + ($data['status'] === 'concluida' ? ['concluida_em' => now()] : []));
        $this->history->record($etapa, $previous, $data['status']);

        return $etapa->fresh();
    }
}
