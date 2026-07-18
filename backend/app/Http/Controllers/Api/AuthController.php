<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AdministrativeEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private readonly AdministrativeEventService $events) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            $this->events->record('auth.login_failed', 'warning', 'autenticacao', 'Tentativa de login inválida.', ['email' => $credentials['email']]);

            return $this->failure('E-mail ou senha inválidos.', ['email' => ['As credenciais informadas são inválidas.']]);
        }

        $this->events->record('auth.login', 'info', 'autenticacao', 'Login realizado com sucesso.', ['authenticated_user_id' => $user->id], $user->id);

        return $this->success(['token' => $user->createToken('frontend')->plainTextToken, 'user' => $this->userData($user)], 'Login realizado com sucesso.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success($this->userData($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->events->record('auth.logout', 'info', 'autenticacao', 'Sessão encerrada pelo usuário.');
        $request->user()?->currentAccessToken()?->delete();

        return $this->success(null, 'Sessão encerrada com sucesso.');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $this->events->record('auth.logout_all', 'warning', 'autenticacao', 'Todas as sessões foram encerradas.');
        $request->user()->tokens()->delete();

        return $this->success(null, 'Todas as sessões foram encerradas.');
    }

    private function userData(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role];
    }
}
