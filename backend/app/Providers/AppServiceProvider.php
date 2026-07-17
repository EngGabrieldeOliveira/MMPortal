<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Cliente;
use App\Models\Orcamento;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Solicitacao;
use App\Models\SolicitacaoAnexo;
use App\Models\User;
use App\Observers\AuditableObserver;
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
        foreach ([Cliente::class, Solicitacao::class, Orcamento::class, Pedido::class, OrdemServico::class, SolicitacaoAnexo::class] as $model) {
            $model::observe(AuditableObserver::class);
        }

        Gate::before(fn (User $user) => $user->hasRole(UserRole::Administrador) ? true : null);
        Gate::define('permission', fn (User $user, string $permission) => $user->hasPermission($permission));
        Gate::define('clientes.view', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Comercial, UserRole::Obras));
        Gate::define('solicitacoes.manage', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Comercial));
        Gate::define('orcamentos.manage', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Comercial));
        Gate::define('ordens-servico.manage', fn (User $user) => $user->hasRole(UserRole::Diretoria, UserRole::Engenharia, UserRole::Producao, UserRole::Obras));
    }
}
