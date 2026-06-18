<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

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
        Paginator::defaultView('partials.pagination');

        Product::observe(ProductObserver::class);
        
        // Registrar Gates para Autorização
        $this->registerGates();
    }

    /**
     * Registrar todos os Gates de autorização do sistema
     */
    private function registerGates(): void
    {
        // Gate para admin - deve ter acesso a tudo
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // ========== PRODUTOS ==========
        Gate::define('produtos.visualizar', function ($user) {
            return $user->hasPermission('produtos.visualizar');
        });

        Gate::define('produtos.cadastrar', function ($user) {
            return $user->hasPermission('produtos.cadastrar');
        });

        Gate::define('produtos.editar', function ($user) {
            return $user->hasPermission('produtos.editar');
        });

        Gate::define('produtos.excluir', function ($user) {
            return $user->hasPermission('produtos.excluir');
        });

        // ========== ESTOQUE ==========
        Gate::define('estoque.visualizar', function ($user) {
            return $user->hasPermission('estoque.visualizar');
        });

        Gate::define('estoque.entradas', function ($user) {
            return $user->hasPermission('estoque.entradas');
        });

        Gate::define('estoque.saidas', function ($user) {
            return $user->hasPermission('estoque.saidas');
        });

        // ========== LOCALIZAÇÃO DE PRODUTOS (NOVO) ==========
        Gate::define('localizacao.visualizar', function ($user) {
            return $user->hasPermission('localizacao.visualizar');
        });

        Gate::define('localizacao.editar', function ($user) {
            return $user->hasPermission('localizacao.editar');
        });

        // ========== CATEGORIAS ==========
        Gate::define('categorias.gerenciar', function ($user) {
            return $user->hasPermission('categorias.gerenciar');
        });

        // ========== FORNECEDORES ==========
        Gate::define('fornecedores.gerenciar', function ($user) {
            return $user->hasPermission('fornecedores.gerenciar');
        });

        // ========== CLIENTES ==========
        Gate::define('clientes.gerenciar', function ($user) {
            return $user->hasPermission('clientes.gerenciar');
        });

        // ========== NOTAS FISCAIS ==========
        Gate::define('notas_fiscais.visualizar', function ($user) {
            return $user->hasPermission('notas_fiscais.visualizar');
        });

        Gate::define('notas_fiscais.emitir', function ($user) {
            return $user->hasPermission('notas_fiscais.emitir');
        });

        Gate::define('notas_fiscais.editar', function ($user) {
            return $user->hasPermission('notas_fiscais.editar');
        });

        // ========== MANIFESTAÇÕES ==========
        Gate::define('manifestacoes.gerenciar', function ($user) {
            return $user->hasPermission('manifestacoes.gerenciar');
        });

        // ========== USUÁRIOS ==========
        Gate::define('usuarios.gerenciar', function ($user) {
            return $user->hasPermission('usuarios.gerenciar');
        });

        // ========== CARGOS ==========
        Gate::define('cargos.gerenciar', function ($user) {
            return $user->hasPermission('cargos.gerenciar');
        });

        // ========== LOGS ==========
        Gate::define('logs.visualizar', function ($user) {
            return $user->hasPermission('logs.visualizar');
        });
    }
}
