<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allowed($user, 'clientes.visualizar', 'clientes.view', [UserRole::Diretoria, UserRole::Comercial, UserRole::Obras]);
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->allowed($user, 'clientes.criar', 'clientes.manage', [UserRole::Diretoria, UserRole::Comercial]);
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $this->allowed($user, 'clientes.editar', 'clientes.manage', [UserRole::Diretoria, UserRole::Comercial]);
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return $this->allowed($user, 'clientes.excluir', 'clientes.manage', [UserRole::Diretoria, UserRole::Comercial]);
    }

    private function allowed(User $user, string $specific, string $legacy, array $roles): bool
    {
        return $user->hasPermission($specific) || $user->hasPermission($legacy) || $user->hasRole(...$roles);
    }
}
