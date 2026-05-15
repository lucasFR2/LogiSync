<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataExportSeeder extends Seeder
{
    public function run()
    {
        // Desativar chaves estrangeiras para evitar erros de ordem
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Dados da tabela roles
        DB::table('roles')->truncate();
        DB::table('roles')->insert([
  0 => 
  [
    'id' => 1,
    'name' => 'Administrador',
    'description' => 'Acesso total ao sistema',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  1 => 
  [
    'id' => 2,
    'name' => 'Gerente de Logística',
    'description' => 'Gestão de estoque e faturamento',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  2 => 
  [
    'id' => 3,
    'name' => 'Supervisor de Armazém',
    'description' => 'Supervisão operacional',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  3 => 
  [
    'id' => 4,
    'name' => 'Operador de Empilhadeira',
    'description' => 'Movimentação de carga',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  4 => 
  [
    'id' => 5,
    'name' => 'Conferente',
    'description' => 'Conferência de entrada e saída',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  5 => 
  [
    'id' => 6,
    'name' => 'Auxiliar de Almoxarifado',
    'description' => 'Suporte operacional',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  6 => 
  [
    'id' => 7,
    'name' => 'Separador (Picker]',
    'description' => 'Separação de pedidos',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  7 => 
  [
    'id' => 8,
    'name' => 'Motorista',
    'description' => 'Transporte e entrega',
    'created_at' => '2026-05-12 10:40:13',
    'updated_at' => '2026-05-12 10:40:13',
  ],
  8 => 
  [
    'id' => 9,
    'name' => 'Recursos Humanos (RH]',
    'description' => 'Gestão de pessoal e cadastro de funcionários',
    'created_at' => '2026-05-12 11:47:41',
    'updated_at' => '2026-05-12 11:47:41',
  ],
]);

        // Dados da tabela permissions
        DB::table('permissions')->truncate();
        DB::table('permissions')->insert([
  0 => 
  [
    'id' => 1,
    'name' => 'products.view',
    'label' => 'Visualizar Produtos',
    'group' => 'Produtos',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  1 => 
  [
    'id' => 2,
    'name' => 'products.create',
    'label' => 'Cadastrar Produtos',
    'group' => 'Produtos',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  2 => 
  [
    'id' => 3,
    'name' => 'products.edit',
    'label' => 'Editar Produtos',
    'group' => 'Produtos',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  3 => 
  [
    'id' => 4,
    'name' => 'products.delete',
    'label' => 'Excluir Produtos',
    'group' => 'Produtos',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  4 => 
  [
    'id' => 5,
    'name' => 'inventory.view',
    'label' => 'Visualizar Estoque',
    'group' => 'Estoque',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  5 => 
  [
    'id' => 6,
    'name' => 'inventory.entry',
    'label' => 'Registrar Entradas',
    'group' => 'Estoque',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  6 => 
  [
    'id' => 7,
    'name' => 'inventory.exit',
    'label' => 'Registrar Saídas',
    'group' => 'Estoque',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  7 => 
  [
    'id' => 8,
    'name' => 'categories.manage',
    'label' => 'Gerenciar Categorias',
    'group' => 'Estoque',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  8 => 
  [
    'id' => 9,
    'name' => 'suppliers.manage',
    'label' => 'Gerenciar Fornecedores',
    'group' => 'Parceiros',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  9 => 
  [
    'id' => 10,
    'name' => 'customers.manage',
    'label' => 'Gerenciar Clientes',
    'group' => 'Parceiros',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  10 => 
  [
    'id' => 11,
    'name' => 'invoices.view',
    'label' => 'Visualizar Notas Fiscais',
    'group' => 'Fiscal',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  11 => 
  [
    'id' => 12,
    'name' => 'invoices.create',
    'label' => 'Emitir Notas Fiscais',
    'group' => 'Fiscal',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  12 => 
  [
    'id' => 13,
    'name' => 'manifests.manage',
    'label' => 'Gerenciar Manifestações',
    'group' => 'Fiscal',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  13 => 
  [
    'id' => 14,
    'name' => 'users.manage',
    'label' => 'Gerenciar Funcionários',
    'group' => 'Administrativo',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  14 => 
  [
    'id' => 15,
    'name' => 'roles.manage',
    'label' => 'Gerenciar Cargos',
    'group' => 'Administrativo',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
  15 => 
  [
    'id' => 16,
    'name' => 'logs.view',
    'label' => 'Visualizar Logs',
    'group' => 'Administrativo',
    'created_at' => '2026-05-12 12:45:57',
    'updated_at' => '2026-05-12 12:45:57',
  ],
]);

        // Dados da tabela users
        DB::table('users')->truncate();
        DB::table('users')->insert([
  0 => 
  [
    'id' => 3,
    'name' => 'Lucas Firmino Rodrigues',
    'email' => 'lucas@logisync.com',
    'email_verified_at' => NULL,
    'password' => '$2y$12$paBA2hVxjUlJz4/eb66Mrei/7cENkxnweOBAGmJJwQzj/jVQc0KkG',
    'remember_token' => NULL,
    'created_at' => '2026-05-12 10:46:59',
    'updated_at' => '2026-05-12 10:46:59',
    'cpf' => '153.012.096-98',
    'role' => 'Administrador',
    'phone' => '(34] 98838-1132',
    'zip_code' => '38500-000',
    'address' => 'Rua João Gonçalves de Souza',
    'number' => '555',
    'neighborhood' => 'Morada Nova',
    'city' => 'Monte Carmelo',
    'state' => 'MG',
    'document_path' => NULL,
    'rg' => NULL,
  ],
  1 => 
  [
    'id' => 4,
    'name' => 'Joao Teste',
    'email' => 'joao@logisync.com',
    'email_verified_at' => NULL,
    'password' => '$2y$12$j6WaEGi9T0TCh4xsCuK.aO9FNmMh5YDdB2IbLAgTreYyEpmqtG5Hi',
    'remember_token' => NULL,
    'created_at' => '2026-05-12 11:02:11',
    'updated_at' => '2026-05-12 12:47:40',
    'cpf' => '123.456.789-00',
    'role' => 'Motorista',
    'phone' => '(34] 98869-8556',
    'zip_code' => '38500-000',
    'address' => 'Rua 1',
    'number' => '654',
    'neighborhood' => 'Centro',
    'city' => 'Monte Carmelo',
    'state' => 'MG',
    'document_path' => 'documents/users/ZlDis3GVAlTJzfLCWi0ig9WOxrkot0Zj8jJrsU98.png',
    'rg' => '12.123.456-8',
  ],
  2 => 
  [
    'id' => 5,
    'name' => 'Usuário de teste',
    'email' => 'teste@logisync.com',
    'email_verified_at' => NULL,
    'password' => '$2y$12$AGldnKHJkSxP..573ajWJuxBwp82a13JASww0NQFtHOYRrsrn9VWa',
    'remember_token' => NULL,
    'created_at' => '2026-05-12 12:58:12',
    'updated_at' => '2026-05-12 12:58:12',
    'cpf' => '456.789.654-32',
    'role' => 'Gerente de Logística',
    'phone' => '(34] 98437-9837',
    'zip_code' => '38500-000',
    'address' => 'Rua João Gonçalves de Souza',
    'number' => '234',
    'neighborhood' => 'Centro',
    'city' => 'Monte Carmelo',
    'state' => 'MG',
    'document_path' => '["documents\\/users\\/Og3RIuIGSTumfCsxTSQRhdUTTPFhRE2SUV6h2kIn.pdf","documents\\/users\\/HoPp4UE4jDYCY8VqorhPEUKwDcJRbmwdEKXGpDcU.pdf","documents\\/users\\/FiSigDEvNBXkKxojsCuiRYfZpbt8rNFUIMX4VpfF.png"]',
    'rg' => '56.465.465-4',
  ],
]);

        // Dados da tabela categories
        DB::table('categories')->truncate();
        DB::table('categories')->insert([
  0 => 
  [
    'id' => 2,
    'name' => 'Eletrônicos',
    'description' => NULL,
    'created_at' => '2026-05-12 09:36:06',
    'updated_at' => '2026-05-12 09:36:06',
  ],
  1 => 
  [
    'id' => 3,
    'name' => 'Ferramentas',
    'description' => 'Ferramentas de manutenção',
    'created_at' => '2026-05-12 09:52:38',
    'updated_at' => '2026-05-12 09:52:38',
  ],
  2 => 
  [
    'id' => 4,
    'name' => 'Alimentos',
    'description' => 'Produtos Alimentícios',
    'created_at' => '2026-05-12 09:52:58',
    'updated_at' => '2026-05-12 09:52:58',
  ],
]);

        // Dados da tabela suppliers
        DB::table('suppliers')->truncate();
        DB::table('suppliers')->insert([
  0 => 
  [
    'id' => 1,
    'name' => 'WN Telecom',
    'contact' => NULL,
    'phone' => '(34] 3512-5500',
    'address' => NULL,
    'email' => 'telecom@wn.com',
    'city' => 'Minas Gerais',
    'state' => 'MG',
    'cnpj' => '12.123.345/0001-12',
    'state_registration' => '123.456.789.110',
    'street' => 'Rua 165',
    'number' => '234',
    'neighborhood' => 'CEntro',
    'zip_code' => '38500-000',
    'created_at' => '2026-05-12 08:38:31',
    'updated_at' => '2026-05-12 12:35:21',
  ],
]);

        // Dados da tabela customers
        DB::table('customers')->truncate();
        DB::table('customers')->insert([
  0 => 
  [
    'id' => 1,
    'name' => 'Lucas Rodrigues',
    'document' => '15315315531',
    'type' => 'individual',
    'email' => 'lucasrodrigues@logisync.com',
    'phone' => '34985625641',
    'state_registration' => NULL,
    'address' => 'Av do Refrigerante',
    'number' => '1',
    'neighborhood' => 'Morada Nova',
    'city' => 'Monte Carmelo',
    'state' => 'MG',
    'zip_code' => '38500-000',
    'created_at' => '2026-05-12 12:36:22',
    'updated_at' => '2026-05-12 12:36:22',
  ],
]);

        // Dados da tabela activity_logs
        DB::table('activity_logs')->truncate();
        DB::table('activity_logs')->insert([
  0 => 
  [
    'id' => 1,
    'user_id' => 4,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:16:39',
    'updated_at' => '2026-05-12 12:16:39',
  ],
  1 => 
  [
    'id' => 2,
    'user_id' => 3,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:16:53',
    'updated_at' => '2026-05-12 12:16:53',
  ],
  2 => 
  [
    'id' => 3,
    'user_id' => 3,
    'action' => 'create_category',
    'description' => 'O usuário criou a categoria: Teste (#5]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:26:01',
    'updated_at' => '2026-05-12 12:26:01',
  ],
  3 => 
  [
    'id' => 4,
    'user_id' => 3,
    'action' => 'delete_category',
    'description' => 'O usuário removeu a categoria: Teste (#5]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:26:38',
    'updated_at' => '2026-05-12 12:26:38',
  ],
  4 => 
  [
    'id' => 5,
    'user_id' => 3,
    'action' => 'update_supplier',
    'description' => 'O usuário alterou o fornecedor: WN Telecom (#1]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:35:21',
    'updated_at' => '2026-05-12 12:35:21',
  ],
  5 => 
  [
    'id' => 6,
    'user_id' => 3,
    'action' => 'create_customer_record',
    'description' => 'O usuário cadastrou o cliente: Lucas Rodrigues (#1]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:36:22',
    'updated_at' => '2026-05-12 12:36:22',
  ],
  6 => 
  [
    'id' => 7,
    'user_id' => 3,
    'action' => 'update_user',
    'description' => 'O usuário alterou os dados do funcionário: Joao Teste (#4]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:38:44',
    'updated_at' => '2026-05-12 12:38:44',
  ],
  7 => 
  [
    'id' => 8,
    'user_id' => 3,
    'action' => 'update_user',
    'description' => 'O usuário alterou os dados do funcionário: Joao Teste (#4]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:39:11',
    'updated_at' => '2026-05-12 12:39:11',
  ],
  8 => 
  [
    'id' => 9,
    'user_id' => 3,
    'action' => 'update_user',
    'description' => 'O usuário alterou os dados do funcionário: Joao Teste (#4]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:43:13',
    'updated_at' => '2026-05-12 12:43:13',
  ],
  9 => 
  [
    'id' => 10,
    'user_id' => 3,
    'action' => 'logout',
    'description' => 'O usuário saiu do sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:43:16',
    'updated_at' => '2026-05-12 12:43:16',
  ],
  10 => 
  [
    'id' => 11,
    'user_id' => 4,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:43:22',
    'updated_at' => '2026-05-12 12:43:22',
  ],
  11 => 
  [
    'id' => 12,
    'user_id' => 4,
    'action' => 'logout',
    'description' => 'O usuário saiu do sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:43:49',
    'updated_at' => '2026-05-12 12:43:49',
  ],
  12 => 
  [
    'id' => 13,
    'user_id' => 3,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:43:56',
    'updated_at' => '2026-05-12 12:43:56',
  ],
  13 => 
  [
    'id' => 14,
    'user_id' => 3,
    'action' => 'update_user',
    'description' => 'O usuário alterou os dados e permissões do funcionário: Joao Teste (#4]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:47:40',
    'updated_at' => '2026-05-12 12:47:40',
  ],
  14 => 
  [
    'id' => 15,
    'user_id' => 3,
    'action' => 'logout',
    'description' => 'O usuário saiu do sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:47:45',
    'updated_at' => '2026-05-12 12:47:45',
  ],
  15 => 
  [
    'id' => 16,
    'user_id' => 4,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:47:53',
    'updated_at' => '2026-05-12 12:47:53',
  ],
  16 => 
  [
    'id' => 17,
    'user_id' => 4,
    'action' => 'logout',
    'description' => 'O usuário saiu do sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:47:56',
    'updated_at' => '2026-05-12 12:47:56',
  ],
  17 => 
  [
    'id' => 18,
    'user_id' => 3,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:48:04',
    'updated_at' => '2026-05-12 12:48:04',
  ],
  18 => 
  [
    'id' => 19,
    'user_id' => 3,
    'action' => 'update_user',
    'description' => 'O usuário alterou os dados e permissões do funcionário: Joao Teste (#4]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:48:31',
    'updated_at' => '2026-05-12 12:48:31',
  ],
  19 => 
  [
    'id' => 20,
    'user_id' => 3,
    'action' => 'logout',
    'description' => 'O usuário saiu do sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:48:43',
    'updated_at' => '2026-05-12 12:48:43',
  ],
  20 => 
  [
    'id' => 21,
    'user_id' => 4,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:48:50',
    'updated_at' => '2026-05-12 12:48:50',
  ],
  21 => 
  [
    'id' => 22,
    'user_id' => 4,
    'action' => 'logout',
    'description' => 'O usuário saiu do sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:49:00',
    'updated_at' => '2026-05-12 12:49:00',
  ],
  22 => 
  [
    'id' => 23,
    'user_id' => 3,
    'action' => 'login',
    'description' => 'O usuário realizou login no sistema.',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:49:07',
    'updated_at' => '2026-05-12 12:49:07',
  ],
  23 => 
  [
    'id' => 24,
    'user_id' => 3,
    'action' => 'update_user',
    'description' => 'O usuário alterou os dados e permissões do funcionário: Joao Teste (#4]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:49:39',
    'updated_at' => '2026-05-12 12:49:39',
  ],
  24 => 
  [
    'id' => 25,
    'user_id' => 3,
    'action' => 'register_user',
    'description' => 'O usuário cadastrou um novo funcionário: Usuário de teste (Gerente de Logística]',
    'details' => NULL,
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64] AppleWebKit/537.36 (KHTML, like Gecko] Chrome/148.0.0.0 Safari/537.36',
    'created_at' => '2026-05-12 12:58:12',
    'updated_at' => '2026-05-12 12:58:12',
  ],
]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
