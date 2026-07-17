<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SolicitacaoController extends Controller
{
    public function show(Solicitacao $solicitacao): JsonResponse
    {
        $solicitacao->load('cliente');
        $solicitacao->setAttribute('anexos', DB::table('solicitacao_anexos')->where('solicitacao_id', $solicitacao->id)->latest()->get());
        return response()->json($solicitacao);
    }

    public function renomearAnexo(Request $request, Solicitacao $solicitacao, int $anexo): JsonResponse
    {
        $dados = $request->validate(['nome_original' => ['required', 'string', 'max:255']]);
        $atualizado = DB::table('solicitacao_anexos')
            ->where('id', $anexo)
            ->where('solicitacao_id', $solicitacao->id)
            ->update(['nome_original' => trim($dados['nome_original']), 'updated_at' => now()]);

        if (! $atualizado) return response()->json(['message' => 'Anexo não encontrado.'], 404);

        return response()->json(DB::table('solicitacao_anexos')->find($anexo));
    }

    public function excluirAnexo(Solicitacao $solicitacao, int $anexo): JsonResponse
    {
        $registro = DB::table('solicitacao_anexos')->where('id', $anexo)->where('solicitacao_id', $solicitacao->id)->first();
        if (! $registro) return response()->json(['message' => 'Anexo não encontrado.'], 404);

        Storage::disk('public')->delete($registro->caminho);
        DB::table('solicitacao_anexos')->where('id', $anexo)->delete();

        return response()->json(null, 204);
    }

    public function visualizarAnexo(Solicitacao $solicitacao, int $anexo)
    {
        $registro = $this->anexoDaSolicitacao($solicitacao, $anexo);
        if (! $registro) return response()->json(['message' => 'Anexo não encontrado.'], 404);

        $caminho = Storage::disk('public')->path($registro->caminho);
        if (! is_file($caminho)) return response()->json(['message' => 'Arquivo não está mais disponível.'], 404);

        return response()->file($caminho, ['Content-Type' => $registro->mime_type]);
    }

    public function baixarAnexo(Solicitacao $solicitacao, int $anexo)
    {
        $registro = $this->anexoDaSolicitacao($solicitacao, $anexo);
        if (! $registro) return response()->json(['message' => 'Anexo não encontrado.'], 404);

        $caminho = Storage::disk('public')->path($registro->caminho);
        if (! is_file($caminho)) return response()->json(['message' => 'Arquivo não está mais disponível.'], 404);

        return response()->download($caminho, $registro->nome_original, ['Content-Type' => $registro->mime_type]);
    }

    private function anexoDaSolicitacao(Solicitacao $solicitacao, int $anexo): ?object
    {
        return DB::table('solicitacao_anexos')->where('id', $anexo)->where('solicitacao_id', $solicitacao->id)->first();
    }
}
