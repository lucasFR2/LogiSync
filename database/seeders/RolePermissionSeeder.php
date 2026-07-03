<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all roles and permissions
        $roles = [
            'Administrador' => ['all'], // Special: gets all permissions
            'Gerente de Logística' => [
                'produtos.visualizar', 'produtos.cadastrar', 'produtos.editar', 'produtos.excluir',
                'estoque.visualizar', 'estoque.entradas', 'estoque.saidas', 'categorias.gerenciar',
                'localizacao.visualizar', 'localizacao.editar',
                'fornecedores.gerenciar', 'clientes.gerenciar', 'transportadoras.gerenciar',
                'notas_fiscais.visualizar', 'notas_fiscais.emitir', 'notas_fiscais.editar',
                'manifestacoes.gerenciar',
                'logs.visualizar'
            ],
            'Supervisor de Armazém' => [
                'produtos.visualizar', 'produtos.editar',
                'estoque.visualizar', 'estoque.entradas', 'estoque.saidas',
                'localizacao.visualizar', 'localizacao.editar',
                'categorias.gerenciar',
                'logs.visualizar'
            ],
            'Operador de Empilhadeira' => [
                'produtos.visualizar',
                'estoque.visualizar', 'estoque.saidas',
                'localizacao.visualizar', 'localizacao.editar'
            ],
            'Conferente' => [
                'produtos.visualizar',
                'estoque.visualizar', 'estoque.entradas', 'estoque.saidas'
            ],
            'Auxiliar de Almoxarifado' => [
                'produtos.visualizar',
                'estoque.visualizar'
            ],
            'Separador (Picker)' => [
                'produtos.visualizar',
                'estoque.visualizar', 'estoque.saidas',
                'localizacao.visualizar'
            ],
            'Recursos Humanos (RH)' => [
                'usuarios.gerenciar', 
                'cargos.gerenciar'
            ],
            'Motorista' => [
                'notas_fiscais.visualizar',
                'manifestacoes.gerenciar'
            ],
        ];

        // Get all permissions for admin
        $allPermissions = Permission::all();
        $adminPermissionIds = $allPermissions->pluck('id')->toArray();

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                continue;
            }

            if (in_array('all', $permissionNames)) {
                // Admin gets all permissions
                $role->permissions()->sync($adminPermissionIds);
                echo "✓ {$roleName}: Todas as permissões atribuídas\n";
            } else {
                // Get permission IDs for this role
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $permissionIds = $permissions->pluck('id')->toArray();
                
                // Sync permissions (removes old, adds new)
                $role->permissions()->sync($permissionIds);
                
                echo "✓ {$roleName}: " . count($permissionIds) . " permissões atribuídas\n";
            }
        }

        echo "\n✅ Permissões atribuídas aos roles com sucesso!\n";
    }
}
