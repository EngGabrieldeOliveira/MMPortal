<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrcamentoController;
use App\Http\Controllers\Api\OrdemServicoController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\SolicitacaoAnexoController;
use App\Http\Controllers\Api\SolicitacaoController;
use App\Http\Controllers\Api\UsuarioOpcaoController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });
});

Route::middleware('auth:sanctum')->prefix('comercial')->group(function (): void {
    Route::get('usuarios/opcoes', [UsuarioOpcaoController::class, 'index']);
    Route::apiResource('clientes', ClienteController::class);
    Route::patch('clientes/{cliente}/status', [ClienteController::class, 'updateStatus']);
    Route::get('clientes/{cliente}/contatos', [ClienteController::class, 'contatos']);
    Route::get('clientes/{cliente}/enderecos', [ClienteController::class, 'enderecos']);
    Route::get('clientes/{cliente}/documentos', [ClienteController::class, 'documentos']);
    Route::apiResource('pedidos', PedidoController::class);

    Route::get('solicitacoes', [SolicitacaoController::class, 'index']);
    Route::post('solicitacoes', [SolicitacaoController::class, 'store']);
    Route::get('solicitacoes/{solicitacao}', [SolicitacaoController::class, 'show']);
    Route::patch('solicitacoes/{solicitacao}', [SolicitacaoController::class, 'update']);
    Route::post('solicitacoes/{solicitacao}/anexos', [SolicitacaoAnexoController::class, 'store']);
    Route::get('solicitacoes/{solicitacao}/anexos/{anexo}/visualizar', [SolicitacaoAnexoController::class, 'view']);
    Route::get('solicitacoes/{solicitacao}/anexos/{anexo}/baixar', [SolicitacaoAnexoController::class, 'download']);
    Route::patch('solicitacoes/{solicitacao}/anexos/{anexo}', [SolicitacaoAnexoController::class, 'update']);
    Route::delete('solicitacoes/{solicitacao}/anexos/{anexo}', [SolicitacaoAnexoController::class, 'destroy']);
    Route::post('solicitacoes/{solicitacao}/orcamentos', [OrcamentoController::class, 'storeFromSolicitacao']);

    Route::get('orcamentos', [OrcamentoController::class, 'index']);
    Route::post('orcamentos', [OrcamentoController::class, 'store']);
    Route::post('orcamentos/{orcamento}/enviar', [OrcamentoController::class, 'send']);
    Route::post('orcamentos/{orcamento}/decisao', [OrcamentoController::class, 'decide']);

    Route::post('pedidos/{pedido}/ordens-servico', [OrdemServicoController::class, 'store']);
    Route::patch('ordens-servico-etapas/{etapa}', [OrdemServicoController::class, 'updateStage']);
});

Route::middleware('auth:sanctum')->get('dashboard', [DashboardController::class, 'index']);
