<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdemServico\StoreOrdemServicoRequest;
use App\Http\Requests\OrdemServico\UpdateOrdemServicoEtapaRequest;
use App\Models\OrdemServicoEtapa;
use App\Models\Pedido;
use App\Services\OrdemServicoService;
use Illuminate\Http\JsonResponse;

class OrdemServicoController extends Controller
{
    public function __construct(private readonly OrdemServicoService $ordens) {}

    public function store(StoreOrdemServicoRequest $request, Pedido $pedido): JsonResponse
    {
        return $this->success($this->ordens->create($pedido, $request->validated()), 'Ordem de serviço criada com sucesso.', 201);
    }

    public function updateStage(UpdateOrdemServicoEtapaRequest $request, OrdemServicoEtapa $etapa): JsonResponse
    {
        return $this->success($this->ordens->updateStage($etapa, $request->validated()), 'Etapa atualizada com sucesso.');
    }
}
