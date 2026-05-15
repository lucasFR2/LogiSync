<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // ========== PRODUTOS ==========
            ['name' => 'produtos.visualizar', 'label' => 'Visualizar Produtos', 'group' => 'Produtos'],
            ['name' => 'produtos.cadastrar', 'label' => 'Cadastrar Produtos', 'group' => 'Produtos'],
            ['name' => 'produtos.editar',   'label' => 'Editar Produtos',    'group' => 'Produtos'],
            ['name' => 'produtos.excluir',  'label' => 'Excluir Produtos',   'group' => 'Produtos'],
            
            // ========== ESTOQUE ==========
            ['name' => 'estoque.visualizar',   'label' => 'Visualizar Estoque',   'group' => 'Estoque'],
            ['name' => 'estoque.entradas',    'label' => 'Registrar Entradas',   'group' => 'Estoque'],
            ['name' => 'estoque.saidas',      'label' => 'Registrar Saídas',     'group' => 'Estoque'],
            ['name' => 'categorias.gerenciar','label' => 'Gerenciar Categorias', 'group' => 'Estoque'],
            
            // ========== LOCALIZAÇÃO (NOVO) ==========
            ['name' => 'localizacao.visualizar', 'label' => 'Visualizar Localização de Produtos', 'group' => 'Localização'],
            ['name' => 'localizacao.editar',     'label' => 'Editar Localização de Produtos',     'group' => 'Localização'],
            
            // ========== PARCEIROS ==========
            ['name' => 'fornecedores.gerenciar', 'label' => 'Gerenciar Fornecedores', 'group' => 'Parceiros'],
            ['name' => 'clientes.gerenciar',     'label' => 'Gerenciar Clientes',     'group' => 'Parceiros'],
            
            // ========== FISCAL / NOTAS FISCAIS ==========
            ['name' => 'notas_fiscais.visualizar',    'label' => 'Visualizar Notas Fiscais',    'group' => 'Fiscal'],
            ['name' => 'notas_fiscais.emitir',        'label' => 'Emitir Notas Fiscais',        'group' => 'Fiscal'],
            ['name' => 'notas_fiscais.editar',        'label' => 'Editar Notas Fiscais',        'group' => 'Fiscal'],
            ['name' => 'manifestacoes.gerenciar',     'label' => 'Gerenciar Manifestações',     'group' => 'Fiscal'],
            
            // ========== ADMINISTRATIVO ==========
            ['name' => 'usuarios.gerenciar', 'label' => 'Gerenciar Funcionários', 'group' => 'Administrativo'],
            ['name' => 'cargos.gerenciar',   'label' => 'Gerenciar Cargos',       'group' => 'Administrativo'],
            ['name' => 'logs.visualizar',    'label' => 'Visualizar Logs',        'group' => 'Administrativo'],
            
            // ========== PERMISSÕES LEGADAS (para compatibilidade) ==========
            ['name' => 'products.view',      'label' => 'Visualizar Produtos (Legado)', 'group' => 'Produtos'],
            ['name' => 'products.create',    'label' => 'Cadastrar Produtos (Legado)',  'group' => 'Produtos'],
            ['name' => 'products.edit',      'label' => 'Editar Produtos (Legado)',     'group' => 'Produtos'],
            ['name' => 'products.delete',    'label' => 'Excluir Produtos (Legado)',    'group' => 'Produtos'],
            ['name' => 'inventory.view',     'label' => 'Visualizar Estoque (Legado)',  'group' => 'Estoque'],
            ['name' => 'inventory.entry',    'label' => 'Registrar Entradas (Legado)',  'group' => 'Estoque'],
            ['name' => 'inventory.exit',     'label' => 'Registrar Saídas (Legado)',    'group' => 'Estoque'],
            ['name' => 'categories.manage',  'label' => 'Gerenciar Categorias (Legado)', 'group' => 'Estoque'],
            ['name' => 'suppliers.manage',   'label' => 'Gerenciar Fornecedores (Legado)', 'group' => 'Parceiros'],
            ['name' => 'customers.manage',   'label' => 'Gerenciar Clientes (Legado)',    'group' => 'Parceiros'],
            ['name' => 'invoices.view',      'label' => 'Visualizar Notas Fiscais (Legado)', 'group' => 'Fiscal'],
            ['name' => 'invoices.create',    'label' => 'Emitir Notas Fiscais (Legado)',     'group' => 'Fiscal'],
            ['name' => 'invoices.edit',      'label' => 'Editar Notas Fiscais (Legado)',     'group' => 'Fiscal'],
            ['name' => 'manifests.manage',   'label' => 'Gerenciar Manifestações (Legado)',  'group' => 'Fiscal'],
            ['name' => 'users.manage',       'label' => 'Gerenciar Funcionários (Legado)',   'group' => 'Administrativo'],
            ['name' => 'roles.manage',       'label' => 'Gerenciar Cargos (Legado)',         'group' => 'Administrativo'],
            ['name' => 'logs.view',          'label' => 'Visualizar Logs (Legado)',          'group' => 'Administrativo'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }
    }
}
