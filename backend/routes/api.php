<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\SolicitacaoController;
use App\Models\Orcamento;
use App\Models\OrdemServicoEtapa;
use App\Models\Pedido;
use App\Models\Solicitacao;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('api.token')->prefix('comercial')->group(function (): void {
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('pedidos', PedidoController::class);

    Route::get('solicitacoes', [WorkflowController::class, 'solicitacoes']);
    Route::get('orcamentos', [WorkflowController::class, 'orcamentos']);
    Route::post('orcamentos', [WorkflowController::class, 'criarOrcamentoDireto']);
    Route::post('solicitacoes', [WorkflowController::class, 'criarSolicitacao']);
    Route::get('solicitacoes/{solicitacao}', [SolicitacaoController::class, 'show']);
    Route::post('solicitacoes/{solicitacao}/anexos', [WorkflowController::class, 'anexarSolicitacao']);
    Route::get('solicitacoes/{solicitacao}/anexos/{anexo}/visualizar', [SolicitacaoController::class, 'visualizarAnexo']);
    Route::get('solicitacoes/{solicitacao}/anexos/{anexo}/baixar', [SolicitacaoController::class, 'baixarAnexo']);
    Route::patch('solicitacoes/{solicitacao}/anexos/{anexo}', [SolicitacaoController::class, 'renomearAnexo']);
    Route::delete('solicitacoes/{solicitacao}/anexos/{anexo}', [SolicitacaoController::class, 'excluirAnexo']);
    Route::patch('solicitacoes/{solicitacao}', [WorkflowController::class, 'alterarSolicitacao']);
    Route::post('solicitacoes/{solicitacao}/orcamentos', [WorkflowController::class, 'criarOrcamento']);
    Route::post('orcamentos/{orcamento}/enviar', [WorkflowController::class, 'enviarOrcamento']);
    Route::post('orcamentos/{orcamento}/decisao', [WorkflowController::class, 'decidirOrcamento']);
    Route::post('pedidos/{pedido}/ordens-servico', [WorkflowController::class, 'criarOrdemServico']);
    Route::patch('ordens-servico-etapas/{etapa}', [WorkflowController::class, 'atualizarEtapa']);
});
