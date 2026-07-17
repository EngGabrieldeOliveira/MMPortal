<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim()->value();
        $clientes = Cliente::query()->when($search, fn ($query) => $query->where(fn ($query) => $query->where('nome', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('empresa', 'like', "%{$search}%")->orWhere('documento', 'like', "%{$search}%")))->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value()))->latest()->paginate(min(max($request->integer('limit', 10), 1), 100));

        return $this->success($clientes);
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        $cliente = Cliente::create($request->validated() + ['codigo' => 'CLI-'.str_pad((string) ((int) Cliente::max('id') + 1), 6, '0', STR_PAD_LEFT)]);

        return $this->success($cliente, 'Cliente criado com sucesso.', 201);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        return $this->success($cliente);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $cliente->update($request->validated());

        return $this->success($cliente->fresh(), 'Cliente atualizado com sucesso.');
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return $this->success(null, 'Cliente removido com sucesso.');
    }
}
