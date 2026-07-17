<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Solicitacao\StoreSolicitacaoRequest;
use App\Http\Requests\Solicitacao\UpdateSolicitacaoRequest;
use App\Models\Solicitacao;
use App\Services\StatusHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolicitacaoController extends Controller
{
    public function __construct(private readonly StatusHistoryService $history) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->string('status')->trim()->value();
        $solicitacoes = Solicitacao::with('cliente')->when($status, fn ($query) => $query->where('status', $status), fn ($query) => $query->whereNotIn('status', ['convertida', 'encerrada']))->latest()->paginate(min(max($request->integer('limit', 10), 1), 100));

        return $this->success($solicitacoes);
    }

    public function store(StoreSolicitacaoRequest $request): JsonResponse
    {
        $solicitacao = Solicitacao::create($request->validated() + ['codigo' => 'SOL-'.str_pad((string) ((int) Solicitacao::max('id') + 1), 6, '0', STR_PAD_LEFT)]);
        $this->history->record($solicitacao, null, 'nova');

        return $this->success($solicitacao, 'Solicitação criada com sucesso.', 201);
    }

    public function show(Solicitacao $solicitacao): JsonResponse
    {
        $solicitacao->load('cliente')->setAttribute('anexos', $solicitacao->anexos()->latest()->get());

        return $this->success($solicitacao);
    }

    public function update(UpdateSolicitacaoRequest $request, Solicitacao $solicitacao): JsonResponse
    {
        $data = $request->validated();
        $previous = $solicitacao->status;
        $solicitacao->update($data);
        if (isset($data['status']) && $data['status'] !== $previous) {
            $this->history->record($solicitacao, $previous, $data['status'], $data['motivo_encerramento'] ?? null);
        }

        return $this->success($solicitacao->fresh(), 'Solicitação atualizada com sucesso.');
    }
}
