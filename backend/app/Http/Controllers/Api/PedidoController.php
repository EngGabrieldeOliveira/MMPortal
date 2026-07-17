<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->string('status')->trim()->value();
        $limit = min(max($request->integer('limit', 10), 1), 100);

        $pedidos = Pedido::query()
            ->with('cliente:id,nome')
            ->withCount('ordensServico')
            ->when($search, fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('cliente', fn ($clientes) => $clientes->where('nome', 'like', "%{$search}%"));
            }))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($limit);

        $pedidos->getCollection()->each(function (Pedido $pedido): void {
            $pedido->setAttribute('cliente_nome', $pedido->cliente->nome);
            $pedido->unsetRelation('cliente');
        });

        return $this->success($pedidos);
    }

    public function store(Request $request): JsonResponse
    {
        $dados = $this->validated($request);
        $pedido = DB::transaction(function () use ($dados): Pedido {
            $pedido = Pedido::create([
                ...$dados,
                'numero' => 'PED-'.str_pad((string) (Pedido::max('id') + 1), 6, '0', STR_PAD_LEFT),
                'data_pedido' => now()->toDateString(),
            ]);

            return $pedido;
        });

        return $this->success($pedido, 'Pedido criado com sucesso.', 201);
    }

    public function show(Pedido $pedido): JsonResponse
    {
        return $this->success($pedido);
    }

    public function update(Request $request, Pedido $pedido): JsonResponse
    {
        $pedido->update($this->validated($request, true));

        return $this->success($pedido->fresh(), 'Pedido atualizado com sucesso.');
    }

    public function destroy(Pedido $pedido): JsonResponse
    {
        $pedido->delete();

        return $this->success(null, 'Pedido removido com sucesso.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'cliente_id' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:clientes,id'],
            'valor_total' => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'gt:0'],
            'quantidade_itens' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::in(['pendente', 'confirmado', 'enviado', 'entregue', 'cancelado'])],
            'data_entrega_prevista' => ['nullable', 'date'],
            'data_entrega_real' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
        ]);
    }
}
