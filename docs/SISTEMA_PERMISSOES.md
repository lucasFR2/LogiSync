# Sistema de Permissões - LogiSync WMS

## ✅ Status: TOTALMENTE FUNCIONAL

Todas as permissões foram corretamente atribuídas aos roles. O sistema de autorização está operacional.

---

## 📋 Permissões por Role

### 1. **Administrador** 
**Permissões: 36** (TODAS)
- ✅ Todos os módulos: Produtos, Estoque, Localização, Parceiros, Fiscal, Administrativo

### 2. **Gerente de Logística**
**Permissões: 17**
- ✅ `produtos.visualizar`, `produtos.cadastrar`, `produtos.editar`, `produtos.excluir`
- ✅ `estoque.visualizar`, `estoque.entradas`, `estoque.saidas`, `categorias.gerenciar`
- ✅ `localizacao.visualizar`, `localizacao.editar`
- ✅ `fornecedores.gerenciar`, `clientes.gerenciar`
- ✅ `notas_fiscais.visualizar`, `notas_fiscais.emitir`, `notas_fiscais.editar`
- ✅ `manifestacoes.gerenciar`
- ✅ `logs.visualizar`

### 3. **Supervisor de Armazém**
**Permissões: 9**
- ✅ `produtos.visualizar`, `produtos.editar`
- ✅ `estoque.visualizar`, `estoque.entradas`, `estoque.saidas`
- ✅ `localizacao.visualizar`, `localizacao.editar`
- ✅ `categorias.gerenciar`
- ✅ `logs.visualizar`

### 4. **Operador de Empilhadeira**
**Permissões: 5**
- ✅ `produtos.visualizar`
- ✅ `estoque.visualizar`, `estoque.saidas`
- ✅ `localizacao.visualizar`, `localizacao.editar`

### 5. **Conferente**
**Permissões: 4**
- ✅ `produtos.visualizar`
- ✅ `estoque.visualizar`, `estoque.entradas`, `estoque.saidas`

### 6. **Auxiliar de Almoxarifado**
**Permissões: 2**
- ✅ `produtos.visualizar`
- ✅ `estoque.visualizar`

### 7. **Separador (Picker)**
**Permissões: 4**
- ✅ `produtos.visualizar`
- ✅ `estoque.visualizar`, `estoque.saidas`
- ✅ `localizacao.visualizar`

### 8. **Recursos Humanos (RH)**
**Permissões: 2**
- ✅ `usuarios.gerenciar`
- ✅ `cargos.gerenciar`

### 9. **Motorista**
**Permissões: 2**
- ✅ `notas_fiscais.visualizar`
- ✅ `manifestacoes.gerenciar`

---

## 🔐 Como Funciona a Autorização

### 1. **Verificação de Permissão** 
Quando um usuário tenta acessar uma rota protegida:

```
User Request → Route Middleware → CheckPermission → User::hasPermission()
   ↓
   Se o usuário tiver a permissão → ✅ Acesso permitido
   Senão → ❌ Erro 403 "Permissão negada"
```

### 2. **Método `hasPermission()`**
O método `User::hasPermission()` funciona assim:

```php
if ($this->isAdmin()) {
    return true;  // Admin tem tudo
}

// Verifica permissões do user ou do role
$userPerms = $this->permissions()->pluck('name')->toArray();
$rolePerms = $this->role->permissions()->pluck('name')->toArray();
$allPerms = array_merge($userPerms, $rolePerms);

return in_array($permission, $allPerms);
```

### 3. **Verificação em Views (Blade)**
Use a diretiva `@can` para mostrar/esconder elementos:

```blade
@can('produtos.visualizar')
    <!-- Mostrar botão de produtos -->
@else
    <!-- Mostrar mensagem de acesso negado -->
@endcan
```

### 4. **Verificação em Controllers (PHP)**
Use o método `authorize()` ou `this->authorize()`:

```php
$this->authorize('produtos.editar', $product);
// Ou manualmente:
if (!auth()->user()->hasPermission('produtos.editar')) {
    abort(403);
}
```

---

## 📊 Fluxo de Permissões

```
1. User é criado com role_id
2. Role tem permissões (many-to-many relationship)
3. User::hasPermission() carrega permissões do role
4. Middleware verifica permissão
5. Policy/Gate autoriza ação
6. Ação é executada
```

---

## 🧪 Teste do Sistema

Para verificar se as permissões estão funcionando:

```bash
# Verificar um usuário específico
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->hasPermission('produtos.visualizar');
=> true/false
```

---

## 🐛 Solução de Problemas

### "Permissão negada" mesmo tendo permissão

**Causas possíveis:**

1. **Usuário sem role atribuído**
   ```php
   $user->role_id // Deve ser NULL se não tiver role
   ```
   - Solução: Atribuir um role ao usuário

2. **Role sem permissões**
   - Verificar: `Role::find(1)->permissions()->count()`
   - Solução: Rodar `php artisan db:seed --class=RolePermissionSeeder`

3. **Middleware não está na rota**
   - Verificar em `routes/web.php` se a rota tem `middleware('permission:...')`
   - Adicionar se faltar

4. **Cache de permissões**
   - A propriedade `$permissionsCache` é carregada uma vez por requisição
   - Se mudou permissões, abrir nova aba/sessão do navegador

---

## 📝 Comandos Úteis

```bash
# Ver todas as permissões
php artisan tinker
>>> App\Models\Permission::all();

# Ver permissões de um role
>>> App\Models\Role::find(1)->permissions;

# Ver permissões de um usuário
>>> App\Models\User::find(1)->permissions;

# Ver role de um usuário  
>>> App\Models\User::find(1)->role;

# Atribuir permissão diretamente a usuário
>>> \$user->permissions()->attach(\$permissionId);

# Remover permissão
>>> \$user->permissions()->detach(\$permissionId);
```

---

## ✨ Resumo das Correções Implementadas

✅ **Criado RolePermissionSeeder.php**
- Atribui permissões apropriadas a cada role
- Administrador recebe todas as permissões
- Outros roles recebem permissões específicas

✅ **Middleware CheckPermission.php**
- Verifica se usuário tem a permissão requerida
- Retorna erro 403 se não tiver

✅ **User Model hasPermission()**
- Carrega permissões do role e user
- Retorna true se admin
- Retorna true se tem a permissão

✅ **Policies**
- Cada policy checa a permissão apropriada
- Delegam para `hasPermission()`

✅ **Gates**
- 20+ gates registrados em AppServiceProvider
- Delegam para `hasPermission()`

---

**Status Final: ✅ SISTEMA DE PERMISSÕES 100% FUNCIONAL**
