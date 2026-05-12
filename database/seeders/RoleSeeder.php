<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'description' => 'Acesso total ao sistema'],
            ['name' => 'Gerente de Logística', 'description' => 'Gestão de estoque e faturamento'],
            ['name' => 'Supervisor de Armazém', 'description' => 'Supervisão operacional'],
            ['name' => 'Operador de Empilhadeira', 'description' => 'Movimentação de carga'],
            ['name' => 'Conferente', 'description' => 'Conferência de entrada e saída'],
            ['name' => 'Auxiliar de Almoxarifado', 'description' => 'Suporte operacional'],
            ['name' => 'Separador (Picker)', 'description' => 'Separação de pedidos'],
            ['name' => 'Recursos Humanos (RH)', 'description' => 'Gestão de pessoal e cadastro de funcionários'],
            ['name' => 'Motorista', 'description' => 'Transporte e entrega'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
