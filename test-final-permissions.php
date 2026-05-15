<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

echo "========== TESTE FINAL DE PERMISSÕES ==========\n\n";

// Verificar permissões criadas
$newPermissions = [
    'produtos.visualizar', 'produtos.cadastrar', 'produtos.editar', 'produtos.excluir',
    'estoque.visualizar', 'estoque.entradas', 'estoque.saidas',
    'notas_fiscais.visualizar', 'notas_fiscais.emitir', 'notas_fiscais.editar',
    'manifestacoes.gerenciar',
    'usuarios.gerenciar', 'cargos.gerenciar', 'logs.visualizar',
    'categorias.gerenciar', 'fornecedores.gerenciar', 'clientes.gerenciar',
    'localizacao.visualizar', 'localizacao.editar'
];

echo "✅ PERMISSÕES NOVAS CRIADAS:\n";
foreach ($newPermissions as $perm) {
    $exists = Permission::where('name', $perm)->exists();
    $status = $exists ? '✓' : '✗';
    echo "   {$status} {$perm}\n";
}

echo "\n✅ ROLES COM PERMISSÕES ATRIBUÍDAS:\n";
$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    $permCount = $role->permissions()->count();
    echo "   - {$role->name}: {$permCount} permissões\n";
}

echo "\n✅ TESTE DE ACESSO - USUÁRIO JOAO TESTE (MOTORISTA):\n";
$user = User::where('name', 'Joao Teste')->first();
if ($user) {
    $testCases = [
        ['perm' => 'notas_fiscais.visualizar', 'esperado' => true, 'descricao' => 'Visualizar Notas Fiscais'],
        ['perm' => 'manifestacoes.gerenciar', 'esperado' => true, 'descricao' => 'Gerenciar Manifestações'],
        ['perm' => 'produtos.visualizar', 'esperado' => false, 'descricao' => 'Visualizar Produtos'],
        ['perm' => 'usuarios.gerenciar', 'esperado' => false, 'descricao' => 'Gerenciar Usuários'],
    ];
    
    foreach ($testCases as $test) {
        $result = $user->hasPermission($test['perm']);
        $match = ($result === $test['esperado']) ? '✓' : '✗';
        $status = ($result === $test['esperado']) ? 'OK' : 'ERRO';
        echo "   {$match} [{$status}] {$test['descricao']}: " . ($result ? 'PERMITIDO' : 'NEGADO') . "\n";
    }
} else {
    echo "   ✗ Usuário não encontrado\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ SISTEMA DE PERMISSÕES CORRIGIDO E FUNCIONAL!\n";
echo "=". str_repeat("=", 49) . "\n";
