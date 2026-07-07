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
            ['name' => 'transportadoras.gerenciar', 'label' => 'Gerenciar Transportadoras', 'group' => 'Parceiros'],
            
            // ========== FISCAL / NOTAS FISCAIS ==========
            ['name' => 'notas_fiscais.visualizar',    'label' => 'Visualizar Notas Fiscais',    'group' => 'Fiscal'],
            ['name' => 'notas_fiscais.emitir',        'label' => 'Emitir Notas Fiscais',        'group' => 'Fiscal'],
            ['name' => 'notas_fiscais.editar',        'label' => 'Editar Notas Fiscais',        'group' => 'Fiscal'],
            ['name' => 'manifestacoes.gerenciar',     'label' => 'Gerenciar Manifestações',     'group' => 'Fiscal'],
            
            // ========== ADMINISTRATIVO ==========
            ['name' => 'usuarios.gerenciar', 'label' => 'Gerenciar Funcionários', 'group' => 'Administrativo'],
            ['name' => 'cargos.gerenciar',   'label' => 'Gerenciar Cargos',       'group' => 'Administrativo'],
            ['name' => 'logs.visualizar',    'label' => 'Visualizar Logs',        'group' => 'Administrativo'],
            ['name' => 'relatorios.visualizar', 'label' => 'Visualizar Relatórios', 'group' => 'Administrativo'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // Delete permissions that are no longer active
        $activeNames = array_column($permissions, 'name');
        Permission::whereNotIn('name', $activeNames)->delete();
    }
}
