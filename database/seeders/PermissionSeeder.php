<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Produtos
            ['name' => 'products.view',   'label' => 'Visualizar Produtos', 'group' => 'Produtos'],
            ['name' => 'products.create', 'label' => 'Cadastrar Produtos', 'group' => 'Produtos'],
            ['name' => 'products.edit',   'label' => 'Editar Produtos',    'group' => 'Produtos'],
            ['name' => 'products.delete', 'label' => 'Excluir Produtos',   'group' => 'Produtos'],
            
            // Estoque
            ['name' => 'inventory.view',   'label' => 'Visualizar Estoque',   'group' => 'Estoque'],
            ['name' => 'inventory.entry',  'label' => 'Registrar Entradas',   'group' => 'Estoque'],
            ['name' => 'inventory.exit',   'label' => 'Registrar Saídas',     'group' => 'Estoque'],
            ['name' => 'categories.manage','label' => 'Gerenciar Categorias', 'group' => 'Estoque'],
            
            // Parceiros
            ['name' => 'suppliers.manage', 'label' => 'Gerenciar Fornecedores', 'group' => 'Parceiros'],
            ['name' => 'customers.manage', 'label' => 'Gerenciar Clientes',     'group' => 'Parceiros'],
            
            // Fiscal
            ['name' => 'invoices.view',    'label' => 'Visualizar Notas Fiscais', 'group' => 'Fiscal'],
            ['name' => 'invoices.create',  'label' => 'Emitir Notas Fiscais',     'group' => 'Fiscal'],
            ['name' => 'invoices.edit',    'label' => 'Editar Notas Fiscais',     'group' => 'Fiscal'],
            ['name' => 'manifests.manage', 'label' => 'Gerenciar Manifestações',  'group' => 'Fiscal'],
            
            // Administrativo
            ['name' => 'users.manage', 'label' => 'Gerenciar Funcionários', 'group' => 'Administrativo'],
            ['name' => 'roles.manage', 'label' => 'Gerenciar Cargos',       'group' => 'Administrativo'],
            ['name' => 'logs.view',    'label' => 'Visualizar Logs',        'group' => 'Administrativo'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }
    }
}
