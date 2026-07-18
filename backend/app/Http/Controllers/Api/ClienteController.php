<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteStatusRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use App\Services\Cliente\ClienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClienteController extends Controller
{
    public function __construct(private readonly ClienteService $clientes) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Cliente::class);
        /** @var LengthAwarePaginator<int, Cliente> $clientes */
        $clientes = $this->clientes->paginate($request->only(['search', 'nome', 'fantasia', 'cnpj', 'cidade', 'contato', 'telefone', 'status', 'classificacao', 'sort', 'direction', 'limit']));
        $clientes->setCollection(ClienteResource::collection($clientes->getCollection())->collection);

        return $this->success($clientes);
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        $this->authorize('create', Cliente::class);

        return $this->success(new ClienteResource($this->clientes->create($request->validated())), 'Cliente criado com sucesso.', 201);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        $this->authorize('view', $cliente);

        return $this->success(new ClienteResource($cliente->load(['classificacoes', 'enderecoCobranca', 'contatos', 'documentos', 'responsavel', 'matriz', 'filiais', 'obras', 'timelineEventos'])));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $this->authorize('update', $cliente);

        return $this->success(new ClienteResource($this->clientes->update($cliente, $request->validated())), 'Cliente atualizado com sucesso.');
    }

    public function updateStatus(UpdateClienteStatusRequest $request, Cliente $cliente): JsonResponse
    {
        $this->authorize('update', $cliente);

        return $this->success(new ClienteResource($this->clientes->update($cliente, $request->validated())), 'Status do cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $this->authorize('delete', $cliente);
        $this->clientes->delete($cliente);

        return $this->success(null, 'Cliente removido com sucesso.');
    }

    public function contatos(Cliente $cliente): JsonResponse
    {
        $this->authorize('view', $cliente);

        return $this->success($cliente->contatos()->paginate());
    }

    public function enderecos(Cliente $cliente): JsonResponse
    {
        $this->authorize('view', $cliente);

        return $this->success($cliente->enderecoCobranca);
    }

    public function documentos(Cliente $cliente): JsonResponse
    {
        $this->authorize('view', $cliente);

        return $this->success($cliente->documentos()->paginate());
    }
}
