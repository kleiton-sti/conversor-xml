<?php

namespace App\Providers;

use App\Models\Permissao;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        if (Schema::hasTable("permissoes")) {
        Permissao::all()->each(function ($permissao) {
            Gate::define($permissao->nome_permissao, function ($user) use ($permissao) {
                // Garante que o relacionamento esteja carregado
                $user->loadMissing('grupo.permissoes');
    
                return $user->grupo &&
                       $user->grupo->permissoes->contains('nome_permissao', $permissao->nome_permissao);
            });
        });

        Paginator::useBootstrapFive();
    }
}
}
