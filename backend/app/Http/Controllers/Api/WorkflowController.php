<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistoricoStatus;
use App\Models\Orcamento;
use App\Models\OrcamentoItem;
use App\Models\OrdemServico;
use App\Models\OrdemServicoEtapa;
use App\Models\Pedido;
use App\Models\Solicitacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function solicitacoes(Request $request): JsonResponse
    {
        $status = $request->string('status')->trim()->value();
        return response()->json(Solicitacao::with('cliente')->when($status, fn ($query) => $query->where('status', $status), fn ($query) => $query->whereNotIn('status', ['convertida', 'encerrada']))->latest()->paginate(min(max($request->integer('limit', 10), 1), 100)));
    }

    public function orcamentos(Request $request): JsonResponse
    {
        $status = $request->string('status')->trim()->value();
        return response()->json(Orcamento::with(['cliente:id,nome,empresa', 'solicitacao:id,nome_contato'])->when($status, fn ($query) => $query->where('status', $status), fn ($query) => $query->whereNotIn('status', ['aceito']))
            ->latest()
            ->paginate(min(max($request->integer('limit', 10), 1), 100)));
    }

    public function criarSolicitacao(Request $request): JsonResponse
    {
        $solicitacao = Solicitacao::create($request->validate([
            'cliente_id' => ['nullable', 'exists:clientes,id'], 'nome_contato' => ['required', 'string', 'max:255'],
            'email_contato' => ['nullable', 'email'], 'telefone_contato' => ['nullable', 'string', 'max:30'],
            'origem' => [Rule::in(['whatsapp', 'email', 'telefone', 'indicacao', 'site', 'visita', 'outro'])],
            'descricao' => ['required', 'string'], 'prazo_desejado' => ['nullable', 'date'], 'proxima_acao_em' => ['nullable', 'date'],
            'responsavel_id' => ['nullable', 'exists:users,id'],
        ]) + ['codigo' => 'SOL-'.str_pad((string) ((int) Solicitacao::max('id') + 1), 6, '0', STR_PAD_LEFT)]);
        $this->historico($solicitacao, null, 'nova');
        return response()->json($solicitacao, 201);
    }

    public function anexarSolicitacao(Request $request, Solicitacao $solicitacao): JsonResponse
    {
        $request->validate([
            'arquivos' => ['required', 'array', 'max:10'],
            'arquivos.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx'],
        ]);

        $anexos = collect($request->file('arquivos'))->map(function ($arquivo) use ($solicitacao): array {
            $caminho = $arquivo->store("solicitacoes/{$solicitacao->id}", 'public');
            return \Illuminate\Support\Facades\DB::table('solicitacao_anexos')->insertGetId([
                'solicitacao_id' => $solicitacao->id, 'nome_original' => $arquivo->getClientOriginalName(), 'caminho' => $caminho,
                'mime_type' => $arquivo->getMimeType(), 'tamanho' => $arquivo->getSize(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        return response()->json(['ids' => $anexos]);
    }

    public function alterarSolicitacao(Request $request, Solicitacao $solicitacao): JsonResponse
    {
        $dados = $request->validate([
            'status' => ['sometimes', Rule::in(['nova', 'em_analise', 'em_orcamento', 'orcamento_enviado', 'negociacao', 'postergada', 'encerrada'])],
            'proxima_acao_em' => ['nullable', 'date'], 'postergada_ate' => ['nullable', 'date'], 'motivo_encerramento' => ['nullable', 'string'],
        ]);
        $anterior = $solicitacao->status;
        $solicitacao->update($dados);
        if (isset($dados['status']) && $dados['status'] !== $anterior) $this->historico($solicitacao, $anterior, $dados['status'], $dados['motivo_encerramento'] ?? null);
        return response()->json($solicitacao->fresh());
    }

    public function criarOrcamento(Request $request, Solicitacao $solicitacao): JsonResponse
    {
        $dados = $request->validate([
            'validade_ate' => ['nullable', 'date'], 'observacoes' => ['nullable', 'string'],
            'itens' => ['required', 'array', 'min:1'], 'itens.*.descricao' => ['required', 'string'],
            'itens.*.quantidade' => ['required', 'numeric', 'gt:0'], 'itens.*.unidade' => ['nullable', 'string', 'max:20'],
            'itens.*.valor_unitario' => ['required', 'numeric', 'min:0'], 'itens.*.especificacoes' => ['nullable', 'array'],
        ]);
        if (! $solicitacao->cliente_id) return response()->json(['message' => 'Vincule um cliente antes de criar o orçamento.'], 422);
        $orcamento = DB::transaction(function () use ($dados, $solicitacao): Orcamento {
            $versao = ((int) $solicitacao->orcamentos()->max('versao')) + 1;
            $orcamento = Orcamento::create(['solicitacao_id' => $solicitacao->id, 'cliente_id' => $solicitacao->cliente_id, 'numero' => 'ORC-'.str_pad((string) (Orcamento::max('id') + 1), 6, '0', STR_PAD_LEFT), 'versao' => $versao, 'validade_ate' => $dados['validade_ate'] ?? null, 'observacoes' => $dados['observacoes'] ?? null]);
            $total = 0;
            foreach ($dados['itens'] as $indice => $item) {
                $valor = $item['quantidade'] * $item['valor_unitario']; $total += $valor;
                OrcamentoItem::create(['orcamento_id' => $orcamento->id, 'ordem' => $indice + 1, 'descricao' => $item['descricao'], 'quantidade' => $item['quantidade'], 'unidade' => $item['unidade'] ?? 'un', 'valor_unitario' => $item['valor_unitario'], 'valor_total' => $valor, 'especificacoes' => $item['especificacoes'] ?? null]);
            }
            $orcamento->update(['valor_total' => $total]);
            return $orcamento;
        });
        $solicitacao->update(['status' => 'convertida']); $this->historico($solicitacao, null, 'convertida');
        return response()->json($orcamento->load('itens'), 201);
    }

    public function criarOrcamentoDireto(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'], 'validade_ate' => ['nullable', 'date'], 'observacoes' => ['nullable', 'string'],
            'itens' => ['required', 'array', 'min:1'], 'itens.*.descricao' => ['required', 'string'], 'itens.*.quantidade' => ['required', 'numeric', 'gt:0'], 'itens.*.unidade' => ['nullable', 'string', 'max:20'], 'itens.*.valor_unitario' => ['required', 'numeric', 'min:0'], 'itens.*.especificacoes' => ['nullable', 'array'],
        ]);
        $orcamento = DB::transaction(function () use ($dados): Orcamento {
            $orcamento = Orcamento::create(['cliente_id' => $dados['cliente_id'], 'numero' => 'ORC-'.str_pad((string) (Orcamento::max('id') + 1), 6, '0', STR_PAD_LEFT), 'valor_total' => 0, 'validade_ate' => $dados['validade_ate'] ?? null, 'observacoes' => $dados['observacoes'] ?? null]);
            $total = 0; foreach ($dados['itens'] as $indice => $item) { $valor = $item['quantidade'] * $item['valor_unitario']; $total += $valor; OrcamentoItem::create(['orcamento_id' => $orcamento->id, 'ordem' => $indice + 1, 'descricao' => $item['descricao'], 'quantidade' => $item['quantidade'], 'unidade' => $item['unidade'] ?? 'un', 'valor_unitario' => $item['valor_unitario'], 'valor_total' => $valor, 'especificacoes' => $item['especificacoes'] ?? null]); }
            $orcamento->update(['valor_total' => $total]); return $orcamento;
        });
        return response()->json($orcamento->load('itens'), 201);
    }

    public function enviarOrcamento(Orcamento $orcamento): JsonResponse
    {
        if ($orcamento->status !== 'rascunho') return response()->json(['message' => 'Apenas orçamentos em rascunho podem ser enviados.'], 422);
        $orcamento->update(['status' => 'enviado', 'enviado_em' => now()]);
        $this->historico($orcamento, 'rascunho', 'enviado');
        return response()->json($orcamento->fresh());
    }

    public function decidirOrcamento(Request $request, Orcamento $orcamento): JsonResponse
    {
        $dados = $request->validate(['decisao' => [Rule::in(['aceito', 'recusado', 'postergado'])], 'motivo' => ['nullable', 'string'], 'postergada_ate' => ['nullable', 'date']]);
        if ($orcamento->status !== 'enviado') return response()->json(['message' => 'Apenas orçamentos enviados podem receber uma decisão.'], 422);
        if ($dados['decisao'] === 'aceito') return $this->converterEmPedido($orcamento);
        $orcamento->update(['status' => $dados['decisao'], 'decidido_em' => now()]);
        $solicitacao = $orcamento->solicitacao;
        $solicitacao->update(['status' => $dados['decisao'] === 'postergado' ? 'postergada' : 'encerrada', 'postergada_ate' => $dados['postergada_ate'] ?? null, 'motivo_encerramento' => $dados['motivo'] ?? null]);
        $this->historico($orcamento, 'enviado', $dados['decisao'], $dados['motivo'] ?? null);
        return response()->json($orcamento->fresh());
    }

    private function converterEmPedido(Orcamento $orcamento): JsonResponse
    {
        $pedido = DB::transaction(function () use ($orcamento): Pedido {
            $orcamento->load('itens');
            $pedido = Pedido::create(['numero' => 'PED-'.str_pad((string) (Pedido::max('id') + 1), 6, '0', STR_PAD_LEFT), 'cliente_id' => $orcamento->cliente_id, 'orcamento_id' => $orcamento->id, 'valor_total' => $orcamento->valor_total, 'quantidade_itens' => $orcamento->itens->count(), 'status' => 'aguardando_pcp', 'data_pedido' => now()->toDateString(), 'observacoes' => $orcamento->observacoes]);
            foreach ($orcamento->itens as $item) $pedido->itens()->create($item->only(['ordem', 'descricao', 'quantidade', 'unidade', 'valor_unitario', 'valor_total', 'especificacoes']) + ['orcamento_item_id' => $item->id]);
            $orcamento->update(['status' => 'aceito', 'decidido_em' => now()]);
            if ($orcamento->solicitacao) $orcamento->solicitacao->update(['status' => 'encerrada']);
            return $pedido;
        });
        $this->historico($orcamento, 'enviado', 'aceito'); $this->historico($pedido, null, 'aguardando_pcp');
        return response()->json($pedido->load('itens'), 201);
    }

    public function criarOrdemServico(Request $request, Pedido $pedido): JsonResponse
    {
        $dados = $request->validate(['titulo' => ['required', 'string', 'max:255'], 'prioridade' => [Rule::in(['baixa', 'normal', 'alta', 'urgente'])], 'prazo_planejado' => ['nullable', 'date'], 'observacoes' => ['nullable', 'string']]);
        $os = DB::transaction(function () use ($pedido, $dados): OrdemServico {
            $os = OrdemServico::create($dados + ['pedido_id' => $pedido->id, 'numero' => 'OS-'.str_pad((string) (OrdemServico::max('id') + 1), 6, '0', STR_PAD_LEFT)]);
            $os->etapas()->createMany([['tipo' => 'fabricacao', 'sequencia' => 1], ['tipo' => 'instalacao', 'sequencia' => 2]]);
            $pedido->update(['status' => 'em_planejamento']);
            return $os;
        });
        $this->historico($os, null, 'aguardando_planejamento');
        return response()->json($os->load('etapas'), 201);
    }

    public function atualizarEtapa(Request $request, OrdemServicoEtapa $etapa): JsonResponse
    {
        $dados = $request->validate(['status' => [Rule::in(['aguardando', 'planejada', 'em_andamento', 'concluida', 'bloqueada'])], 'observacoes' => ['nullable', 'string'], 'planejada_inicio_em' => ['nullable', 'date'], 'planejada_fim_em' => ['nullable', 'date']]);
        if ($etapa->tipo === 'instalacao' && $dados['status'] === 'em_andamento' && $etapa->ordemServico->etapas()->where('tipo', 'fabricacao')->where('status', '!=', 'concluida')->exists()) return response()->json(['message' => 'A fabricação deve ser concluída antes de iniciar a instalação.'], 422);
        $anterior = $etapa->status;
        $etapa->update($dados + ($dados['status'] === 'em_andamento' ? ['iniciada_em' => now()] : []) + ($dados['status'] === 'concluida' ? ['concluida_em' => now()] : []));
        $this->historico($etapa, $anterior, $dados['status']);
        return response()->json($etapa->fresh());
    }

    private function historico(object $modelo, ?string $anterior, string $novo, ?string $motivo = null): void
    {
        HistoricoStatus::create(['historico_type' => $modelo::class, 'historico_id' => $modelo->id, 'status_anterior' => $anterior, 'status_novo' => $novo, 'motivo' => $motivo]);
    }
}
