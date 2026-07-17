<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orcamento\DecidirOrcamentoRequest;
use App\Http\Requests\Orcamento\StoreOrcamentoDaSolicitacaoRequest;
use App\Http\Requests\Orcamento\StoreOrcamentoRequest;
use App\Models\Orcamento;
use App\Models\Solicitacao;
use App\Services\OrcamentoService;
use App\Services\StatusHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrcamentoController extends Controller
{
    public function __construct(private readonly OrcamentoService $orcamentos, private readonly StatusHistoryService $history) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->string('status')->trim()->value();
        $orcamentos = Orcamento::with(['cliente:id,nome,empresa', 'solicitacao:id,nome_contato'])->when($status, fn ($query) => $query->where('status', $status), fn ($query) => $query->whereNotIn('status', ['aceito']))->latest()->paginate(min(max($request->integer('limit', 10), 1), 100));

        return $this->success($orcamentos);
    }

    public function store(StoreOrcamentoRequest $request): JsonResponse
    {
        return $this->success($this->orcamentos->create($request->validated()), 'Orçamento criado com sucesso.', 201);
    }

    public function storeFromSolicitacao(StoreOrcamentoDaSolicitacaoRequest $request, Solicitacao $solicitacao): JsonResponse
    {
        if (! $solicitacao->cliente_id) {
            return $this->failure('Vincule um cliente antes de criar o orçamento.');
        }
        $orcamento = $this->orcamentos->create($request->validated(), $solicitacao);
        $solicitacao->update(['status' => 'convertida']);
        $this->history->record($solicitacao, 'em_analise', 'convertida');

        return $this->success($orcamento, 'Solicitação convertida em orçamento.', 201);
    }

    public function send(Orcamento $orcamento): JsonResponse
    {
        return $this->success($this->orcamentos->send($orcamento), 'Orçamento enviado com sucesso.');
    }

    public function decide(DecidirOrcamentoRequest $request, Orcamento $orcamento): JsonResponse
    {
        $result = $this->orcamentos->decide($orcamento, $request->validated());

        return $this->success($result, $result instanceof Orcamento ? 'Decisão registrada com sucesso.' : 'Orçamento convertido em pedido.');
    }
}
