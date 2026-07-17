<?php

use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\EnsurePermission;
use App\Services\AdministrativeEventService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(AssignRequestId::class);
        $middleware->alias(['permission' => EnsurePermission::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->is('api/*'));
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['data' => null, 'message' => 'Os dados informados são inválidos.', 'errors' => $exception->errors()], 422);
            }
        });
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['data' => null, 'message' => 'Não autenticado.', 'errors' => (object) []], 401);
            }
        });
        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['data' => null, 'message' => 'Você não possui permissão para esta ação.', 'errors' => (object) []], 403);
            }
        });
        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['data' => null, 'message' => 'Registro não encontrado.', 'errors' => (object) []], 404);
            }
        });
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['data' => null, 'message' => $exception->getMessage() ?: 'A requisição não pôde ser processada.', 'errors' => (object) []], $exception->getStatusCode());
            }
        });
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/*') || $exception instanceof ValidationException || $exception instanceof AuthenticationException || $exception instanceof AuthorizationException || $exception instanceof ModelNotFoundException || $exception instanceof HttpExceptionInterface) {
                return null;
            }
            Log::error('api.unhandled_exception', ['exception' => $exception::class, 'message' => $exception->getMessage(), 'request_id' => $request->attributes->get('request_id')]);
            try {
                app(AdministrativeEventService::class)->record('system.exception', 'error', 'sistema', 'Exceção interna tratada.', ['exception' => $exception::class]);
            } catch (Throwable $loggingException) {
                Log::critical('api.exception_event_failed', ['exception' => $loggingException::class]);
            }

            return response()->json(['data' => null, 'message' => 'Erro interno. Tente novamente mais tarde.', 'errors' => (object) []], 500);
        });
    })->create();
