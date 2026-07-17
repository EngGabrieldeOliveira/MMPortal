<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Solicitacao\RenameSolicitacaoAnexoRequest;
use App\Http\Requests\Solicitacao\StoreSolicitacaoAnexosRequest;
use App\Models\Solicitacao;
use App\Models\SolicitacaoAnexo;
use App\Services\AdministrativeEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SolicitacaoAnexoController extends Controller
{
    public function __construct(private readonly AdministrativeEventService $events) {}

    public function store(StoreSolicitacaoAnexosRequest $request, Solicitacao $solicitacao): JsonResponse
    {
        $anexos = collect($request->file('arquivos'))->map(fn ($file) => $solicitacao->anexos()->create(['nome_original' => $file->getClientOriginalName(), 'caminho' => $file->store("solicitacoes/{$solicitacao->id}", 'public'), 'mime_type' => $file->getMimeType(), 'tamanho' => $file->getSize()]));
        $this->events->record('file.upload', 'info', 'comercial.solicitacoes', 'Documentos anexados à solicitação.', ['solicitacao_id' => $solicitacao->id, 'total' => $anexos->count()]);

        return $this->success($anexos, 'Documentos anexados com sucesso.', 201);
    }

    public function update(RenameSolicitacaoAnexoRequest $request, Solicitacao $solicitacao, SolicitacaoAnexo $anexo): JsonResponse
    {
        abort_unless($anexo->solicitacao_id === $solicitacao->id, 404);
        $anexo->update(['nome_original' => trim($request->validated('nome_original'))]);

        return $this->success($anexo->fresh(), 'Documento renomeado com sucesso.');
    }

    public function destroy(Solicitacao $solicitacao, SolicitacaoAnexo $anexo): JsonResponse
    {
        abort_unless($anexo->solicitacao_id === $solicitacao->id, 404);
        $anexo->delete();

        return $this->success(null, 'Documento removido com sucesso.');
    }

    public function view(Solicitacao $solicitacao, SolicitacaoAnexo $anexo)
    {
        abort_unless($anexo->solicitacao_id === $solicitacao->id, 404);

        return response()->file(Storage::disk('public')->path($anexo->caminho), ['Content-Type' => $anexo->mime_type]);
    }

    public function download(Solicitacao $solicitacao, SolicitacaoAnexo $anexo)
    {
        abort_unless($anexo->solicitacao_id === $solicitacao->id, 404);

        return response()->download(Storage::disk('public')->path($anexo->caminho), $anexo->nome_original, ['Content-Type' => $anexo->mime_type]);
    }
}
