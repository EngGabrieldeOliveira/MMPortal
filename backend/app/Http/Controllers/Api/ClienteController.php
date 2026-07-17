<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();
        $limit = min(max($request->integer('limit', 10), 1), 100);

        $clientes = Cliente::query()
            ->when($search, fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('empresa', 'like', "%{$search}%")
                    ->orWhere('documento', 'like', "%{$search}%");
            }))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($limit);

        return response()->json($clientes);
    }

    public function store(Request $request): JsonResponse
    {
        $cliente = Cliente::create($this->validated($request) + [
            'codigo' => 'CLI-'.str_pad((string) ((int) Cliente::max('id') + 1), 6, '0', STR_PAD_LEFT),
        ]);

        return response()->json($cliente, 201);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        return response()->json($cliente);
    }

    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        $cliente->update($this->validated($request, $cliente));

        return response()->json($cliente->fresh());
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json(null, 204);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Cliente $cliente = null): array
    {
        return $request->validate([
            'tipo' => [$cliente ? 'sometimes' : 'required', Rule::in(['fisica', 'juridica'])],
            'nome' => [$cliente ? 'sometimes' : 'required', 'string', 'max:255'],
            'email' => [$cliente ? 'sometimes' : 'required', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($cliente)],
            'documento' => ['nullable', 'string', 'max:18', Rule::unique('clientes', 'documento')->ignore($cliente)],
            'telefone' => [$cliente ? 'sometimes' : 'required', 'string', 'max:30'],
            'empresa' => [$cliente ? 'sometimes' : 'required', 'string', 'max:255'],
            'cidade' => [$cliente ? 'sometimes' : 'required', 'string', 'max:255'],
            'estado' => [$cliente ? 'sometimes' : 'required', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['ativo', 'inativo', 'suspenso'])],
        ]);
    }
}
