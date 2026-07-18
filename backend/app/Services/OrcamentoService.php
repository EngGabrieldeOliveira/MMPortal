<?php

namespace App\Services;

use App\Models\Orcamento;
use App\Models\OrcamentoItem;
use App\Models\Pedido;
use App\Models\Solicitacao;
use Illuminate\Support\Facades\DB;

class OrcamentoService
{
    public function __construct(private readonly StatusHistoryService $history) {}

    public function create(array $data, ?Solicitacao $solicitacao = null): Orcamento
    {
        return DB::transaction(function () use ($data, $solicitacao): Orcamento {
            $orcamento = Orcamento::create(['solicitacao_id' => $solicitacao?->id, 'cliente_id' => $solicitacao ? $solicitacao->cliente_id : $data['cliente_id'], 'numero' => 'ORC-'.str_pad((string) ((int) Orcamento::max('id') + 1), 6, '0', STR_PAD_LEFT), 'versao' => $solicitacao ? ((int) $solicitacao->orcamentos()->max('versao')) + 1 : 1, 'validade_ate' => $data['validade_ate'] ?? null, 'observacoes' => $data['observacoes'] ?? null]);
            $total = 0;
            foreach ($data['itens'] as $index => $item) {
                $value = $item['quantidade'] * $item['valor_unitario'];
                $total += $value;
                OrcamentoItem::create(['orcamento_id' => $orcamento->id, 'ordem' => $index + 1, 'descricao' => $item['descricao'], 'quantidade' => $item['quantidade'], 'unidade' => $item['unidade'] ?? 'un', 'valor_unitario' => $item['valor_unitario'], 'valor_total' => $value, 'especificacoes' => $item['especificacoes'] ?? null]);
            }
            $orcamento->update(['valor_total' => $total]);

            return $orcamento->load('itens');
        });
    }

    public function send(Orcamento $orcamento): Orcamento
    {
        if ($orcamento->status !== 'rascunho') {
            abort(422, 'Apenas orçamentos em rascunho podem ser enviados.');
        }
        $orcamento->update(['status' => 'enviado', 'enviado_em' => now()]);
        $this->history->record($orcamento, 'rascunho', 'enviado');

        return $orcamento->fresh();
    }

    public function decide(Orcamento $orcamento, array $data): Orcamento|Pedido
    {
        if ($orcamento->status !== 'enviado') {
            abort(422, 'Apenas orçamentos enviados podem receber uma decisão.');
        }
        if ($data['decisao'] === 'aceito') {
            return $this->convertToOrder($orcamento);
        }
        $orcamento->update(['status' => $data['decisao'], 'decidido_em' => now()]);
        if ($orcamento->solicitacao) {
            $orcamento->solicitacao->update(['status' => $data['decisao'] === 'postergado' ? 'postergada' : 'encerrada', 'postergada_ate' => $data['postergada_ate'] ?? null, 'motivo_encerramento' => $data['motivo'] ?? null]);
        }
        $this->history->record($orcamento, 'enviado', $data['decisao'], $data['motivo'] ?? null);

        return $orcamento->fresh();
    }

    private function convertToOrder(Orcamento $orcamento): Pedido
    {
        return DB::transaction(function () use ($orcamento): Pedido {
            $orcamento->load('itens');
            $pedido = Pedido::create(['numero' => 'PED-'.str_pad((string) ((int) Pedido::max('id') + 1), 6, '0', STR_PAD_LEFT), 'cliente_id' => $orcamento->cliente_id, 'orcamento_id' => $orcamento->id, 'valor_total' => $orcamento->valor_total, 'quantidade_itens' => $orcamento->itens->count(), 'status' => 'aguardando_pcp', 'data_pedido' => now()->toDateString(), 'observacoes' => $orcamento->observacoes]);
            foreach ($orcamento->itens as $item) {
                $pedido->itens()->create($item->only(['ordem', 'descricao', 'quantidade', 'unidade', 'valor_unitario', 'valor_total', 'especificacoes']) + ['orcamento_item_id' => $item->getAttribute('id')]);
            }
            $orcamento->update(['status' => 'aceito', 'decidido_em' => now()]);
            $orcamento->solicitacao?->update(['status' => 'encerrada']);
            $this->history->record($orcamento, 'enviado', 'aceito');
            $this->history->record($pedido, null, 'aguardando_pcp');

            return $pedido->load('itens');
        });
    }
}
