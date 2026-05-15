# 🧪 GUIA DE TESTES - SISTEMA DE PERMISSÕES

## 1. TESTE DO SISTEMA EM FUNCIONAMENTO

### 1.1 Verificar Permissões no Banco
```bash
# Conectar no MySQL
mysql -u root -p logisync

# Ver todas as permissões
SELECT COUNT(*) FROM permissions;
# Esperado: 36 permissões

# Ver por grupo
SELECT DISTINCT `group` FROM permissions ORDER BY `group`;
# Esperado: Localização, Produtos, Estoque, Parceiros, Fiscal, Administrativo, + Legadas
```

### 1.2 Verificar Relação User-Role
```bash
# No MySQL:
DESCRIBE users;
# Esperado: Coluna `role_id` (bigint) e coluna `role` (string) deve estar AUSENTE

SELECT * FROM users WHERE id=1\G
# Esperado: role_id preenchido com ID de um role
```

### 1.3 Testar Model Relationships
```bash
php artisan tinker

# Verificar User-Role
$user = User::with('role')->first();
$user->role  # Esperado: Role object

# Verificar User-Permission
$user->permissions  # Esperado: Collection de Permissions

# Verificar hasPermission method
$user->hasPermission('produtos.visualizar')  # Esperado: true/false
```

---

## 2. TESTE DE ROTAS PROTEGIDAS

### 2.1 Testar Acesso Sem Autenticação
```bash
curl -X GET http://localhost:8000/products
# Esperado: 302 Redirect para /login
```

### 2.2 Testar Acesso Com Autenticação Mas Sem Permissão
```bash
# 1. Fazer login como usuário comum (não admin)
# 2. Ir para: http://localhost:8000/roles
# Esperado: Erro 403 Forbidden ou tela de acesso negado

# 3. Verificar no Developer Tools → Network → Status: 403
```

### 2.3 Testar Acesso Com Permissão
```bash
# 1. Fazer login como admin
# 2. Ir para: http://localhost:8000/products
# Esperado: Lista de produtos carrega normalmente (200 OK)
```

### 2.4 Testar Proteção de APIs
```bash
# Via Postman ou curl

# Sem autenticação
curl -X GET http://localhost:8000/products
# Esperado: 302 redirect

# Com autenticação mas sem permissão
curl -X GET http://localhost:8000/products \
  -H "Authorization: Bearer $TOKEN"
# Esperado: 403 JSON response

# Exemplo resposta 403:
# {"message": "This action is unauthorized."}
```

---

## 3. TESTE DE POLICIES

### 3.1 Testar Product Policy
```bash
php artisan tinker

# Criar um usuário sem permissão
$user = User::find(2);  # User sem produtos.editar

# Testar policy
Gate::authorize('produtos.editar')  # Esperado: AuthorizationException
// Ou usar Policy diretamente:
$product = Product::first();
$user->can('update', $product)  # Esperado: false

# Testar com admin
$admin = User::find(1);
$admin->can('update', $product)  # Esperado: true
```

### 3.2 Testar Authorization em Controller
```bash
# Modificar ProductController::edit() temporary para testar:
public function edit(Product $product)
{
    $this->authorize('update', $product);  // Isso vai lançar 403 se sem permissão
    // ...
}

# Testar:
# - Usuário com permissão: Abre edit page (200 OK)
# - Usuário sem permissão: Erro 403
```

---

## 4. TESTE DE LOCALIZAÇÃO DE PRODUTOS

### 4.1 Verificar Campos no Banco
```bash
mysql -u root -p logisync
DESCRIBE products;
# Esperado: warehouse_location_code, aisle, shelf, level, box, location_notes, location_updated_at
```

### 4.2 Testar Métodos de Localização
```bash
php artisan tinker

$product = Product::first();

# Testar getFormattedLocation
$product->getFormattedLocation()
# Esperado: "Corredor A / Prateleira 01 / Nível 3" ou null

# Testar getLocationComponents
$product->getLocationComponents()
# Esperado: Array com todos os componentes

# Testar updateLocation
$product->updateLocation([
    'warehouse_location_code' => 'A-01-03',
    'aisle' => 'A',
    'shelf' => '01',
    'level' => '3',
    'box' => '5',
    'location_notes' => 'Teste de localização'
]);
$product->location_updated_at  # Esperado: now() timestamp
```

### 4.3 Testar Permissões de Localização
```bash
php artisan tinker

$product = Product::first();
$user = User::find(2);  # User sem localizacao.visualizar

# Testar se pode ver localização
$user->can('viewLocation', $product)  # Esperado: false

# Testar se pode editar localização
$user->can('editLocation', $product)  # Esperado: false
```

---

## 5. TESTE DE AUDITORIA

### 5.1 Gerar Acesso Negado e Verificar Log
```bash
# 1. Fazer login como usuário comum
# 2. Ir para http://localhost:8000/roles
# 3. Aguardar erro 403
# 4. Verificar no banco:

SELECT * FROM activity_logs WHERE action='forbidden_access' ORDER BY created_at DESC LIMIT 1\G
# Esperado: Log contendo username, rota, IP
```

### 5.2 Verificar Logs de Ações
```bash
# No MySQL:
SELECT COUNT(*) FROM activity_logs;
# Esperado: >0

SELECT DISTINCT action FROM activity_logs;
# Esperado: Incluir 'forbidden_access', 'create_role', 'update_role', etc.
```

---

## 6. TESTE DE INTERFACE (Frontend)

### 6.1 Testar Ocultação de Botões
```bash
# Fazer login como usuário SEM permissão de editar
# Abrir página de produtos
# Verificar que NÃO vê botão "Editar"
# Ainda assim, tentar forçar edição via URL → deve retornar 403
```

### 6.2 Testar Sidebar Dinâmica
```bash
# Fazer login com diferentes usuários
# Verificar que sidebar mostra apenas opções permitidas
# Exemplo:
#   - Sem 'produtos.visualizar' → Não vê "Produtos"
#   - Sem 'cargos.gerenciar' → Não vê "Cargos"
```

---

## 7. CENÁRIOS COMPLETOS

### Cenário 1: Usuário Apenas Visualizador
**Setup:**
- Usuario: João da Silva
- Role: Visualizador
- Permissões: produtos.visualizar, estoque.visualizar

**Testes:**
- ✅ Pode abrir /products
- ✅ Pode abrir /inventory
- ❌ Não pode criar produtos (403 se tentar)
- ❌ Não pode editar produtos (botão oculto + backend 403)
- ❌ Não pode ver /roles (403)
- ❌ Não pode ver /users (403)

### Cenário 2: Usuário Gerente de Estoque
**Setup:**
- Usuario: Maria Santos
- Role: Gerente de Estoque
- Permissões: estoque.visualizar, estoque.entradas, estoque.saidas, localizacao.visualizar, localizacao.editar

**Testes:**
- ✅ Pode visualizar estoque
- ✅ Pode registrar entradas
- ✅ Pode registrar saídas
- ✅ Pode visualizar localização de produtos
- ✅ Pode editar localização de produtos
- ❌ Não pode criar/editar produtos
- ❌ Não pode gerenciar usuários
- ❌ Não pode ver admin area

### Cenário 3: Admin Completo
**Setup:**
- Usuario: Admin User
- Role: Administrador
- Permissões: Todas

**Testes:**
- ✅ Pode acessar TUDO
- ✅ Pode criar/editar/excluir qualquer coisa
- ✅ Pode gerenciar usuários e cargos
- ✅ Pode ver todos os logs

---

## 8. CHECKLIST DE VALIDAÇÃO

```
ESTRUTURA DE DADOS
☐ Coluna role_id existe em users
☐ Coluna role (string) foi removida
☐ Foreign key role_id→roles.id funciona

PERMISSÕES
☐ 36+ permissões cadastradas no banco
☐ Permissões distribuídas em 6+ grupos
☐ Permissões novas de localização existem

GATES & POLICIES
☐ AppServiceProvider registra 20+ gates
☐ ProductPolicy criada com métodos corretos
☐ InvoicePolicy, UserPolicy, etc. existem
☐ Métodos viewLocation/editLocation em ProductPolicy

ROTAS PROTEGIDAS
☐ /products requer 'produtos.visualizar'
☐ /invoices requer 'notas_fiscais.visualizar'
☐ /roles requer 'cargos.gerenciar'
☐ Acesso sem permissão retorna 403

AUDITORIA
☐ LogForbiddenAccess middleware existe
☐ Tentativas de 403 são registradas
☐ Logs aparecem em activity_logs table

LOCALIZAÇÃO
☐ 7 campos adicionados em products
☐ getFormattedLocation() funciona
☐ updateLocation() funciona
☐ Permissões de localização funcionam

COMPATIBILIDADE
☐ Nomes legados ainda funcionam
☐ hasPermission() ainda funciona
☐ Sem erros em migration
☐ Sistema bootea sem erros
```

---

## 9. COMANDOS ÚTEIS

```bash
# Listar rotas com middlewares
php artisan route:list | grep "permission"

# Testar middleware
php artisan tinker
Gate::allows('produtos.visualizar')  # true/false
Auth::user()->hasPermission('produtos.visualizar')

# Ver migrations status
php artisan migrate:status

# Executar seeder novamente
php artisan db:seed --class=PermissionSeeder

# Limpar cache (se usar cache de permissões)
php artisan cache:clear

# Ver erros de sintaxe
php -l app/Models/Product.php
php -l app/Policies/ProductPolicy.php
```

---

## 10. TROUBLESHOOTING

### Problema: 403 Forbidden mesmo com permissão
**Solução:**
1. Verificar que Role está vinculada ao User
2. Verificar que Permission está vinculada ao Role
3. Limpar cache: `php artisan cache:clear`
4. Fazer logout e login novamente

### Problema: Permissões não aparecem no banco
**Solução:**
```bash
php artisan db:seed --class=PermissionSeeder
php artisan cache:clear
```

### Problema: Erro "relation not found" em User::role()
**Solução:**
1. Verificar que migration foi executada: `php artisan migrate:status`
2. Verificar coluna role_id existe: `DESCRIBE users`
3. Reexecutar se necessário: `php artisan migrate:refresh --seed`

---

## Pronto para Testar!

Siga os testes acima para validar completamente o sistema de permissões. 
Qualquer resultado inesperado = reportar error específico.
