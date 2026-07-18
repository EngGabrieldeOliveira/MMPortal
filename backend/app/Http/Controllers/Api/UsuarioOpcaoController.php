<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UsuarioOpcaoController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Cliente::class);

        $users = User::query()
            ->whereIn('role', [UserRole::Administrador, UserRole::Diretoria, UserRole::Comercial])
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->success($users);
    }
}
