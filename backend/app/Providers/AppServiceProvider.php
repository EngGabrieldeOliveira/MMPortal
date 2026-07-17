<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(fn (User $user) => $user->hasRole(UserRole::Administrador) ? true : null);
        Gate::define('clientes.view', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Comercial, UserRole::Obras));
        Gate::define('solicitacoes.manage', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Comercial));
        Gate::define('orcamentos.manage', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Comercial));
        Gate::define('ordens-servico.manage', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Engenharia, UserRole::Producao, UserRole::Obras));
    }
}
