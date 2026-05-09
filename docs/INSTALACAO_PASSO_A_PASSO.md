# 🔧 Instruções de Instalação - Passo a Passo

## Coloque este arquivo na raiz do projeto e siga os passos

---

## ✅ PASSO 1: Verificar Requisitos

Certifique-se que você tem:

```bash
# Verificar PHP
php -v
# Deve retornar: PHP 8.1 ou superior

# Verificar Composer
composer --version
# Deve retornar: Composer version 2.x

# Verificar Laravel
php artisan --version
# Deve retornar: Laravel Framework 11.x
```

Se algum comando não funcionar, instale antes de continuar.

---

## ✅ PASSO 2: Estrutura Está Completa?

Verifique se os arquivos foram criados corretamente:

```bash
# Verificar Models
ls app/Models/
# Deve mostrar: Product.php e Inventory.php

# Verificar Controller
ls app/Http/Controllers/ | grep Product
# Deve mostrar: ProductController.php

# Verificar Views
ls resources/views/products/
# Deve mostrar: index, create, edit, show

# Verificar Migrations
ls database/migrations/ | grep -i product
# Deve mostrar: 2024_01_15_000000_create_products_table.php
# Deve mostrar: 2024_01_15_000001_create_inventories_table.php
```

---

## ✅ PASSO 3: Executar as Migrações

🔥 **ESTE É O PASSO MAIS IMPORTANTE!**

```bash
# Executar todas as migrações
php artisan migrate

# Você verá algo como:
# Migration: 2024_01_15_000000_create_products_table
# Migration: 2024_01_15_000001_create_inventories_table
# Migrated successfully
```

✅ **Se viu "Migrated successfully", está tudo OK!**

❌ **Se viu erro**, verifique:
- Banco de dados está rodando?
- Arquivo `.env` tem DB_CONNECTION correto?
- Tabelas anterior não existem? (não há conflito)

---

## ✅ PASSO 4: Limpar Cache (Recomendado)

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Isso garante que tudo está carregado corretamente.

---

## ✅ PASSO 5: Iniciar o Servidor

```bash
php artisan serve
```

Você verá:

```
   INFO  Server running on [http://127.0.0.1:8000]

  Press Ctrl+C to quit
```

Mantenha isso rodando em um terminal!

---

## ✅ PASSO 6: Acessar no Navegador

Abra seu navegador:

```
http://localhost:8000/login
```

ou se usando em servidor:

```
http://seu-dominio.com/login
```

---

## ✅ PASSO 7: Fazer Login

Faça login com suas credenciais.

Se não tiver usuário, você pode criar um por:

```bash
# Em outro terminal, execute:
php artisan tinker

# Dentro do tinker, execute:
$user = App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin'
]);

# Digite: exit para sair
```

---

## ✅ PASSO 8: Acessar o Sistema de Produtos

No menu lateral esquerdo:

```
Clique em: "Produtos"
```

Você deve ver:
- Listagem vazia (pois não há produtos ainda)
- Botão "+ Novo Produto"
- Barra de busca

✅ **Se viu isso, está funcionar perfeito!**

---

## ✅ PASSO 9: Cadastrar Primeiro Produto

1. Clique em **"+ Novo Produto"**
2. Preencha:
   - **Nome**: "Notebook Dell"
   - **SKU**: "NB-DELL-001"
   - **Preço Unitário**: "2500"
   - **Quantidade**: "10"
   - **Nível de Ressuprimento**: "5"
3. Clique em **"Cadastrar Produto"**

✅ Se viu mensagem verde "Produto criado com sucesso!", perfeito!

---

## ✅ PASSO 10: Testar Entrada

1. Clique no produto que acabou de criar
2. Procure pelo painel verde "Registrar Entrada"
3. Digite "5" em Quantidade
4. Clique "Confirmar Entrada"

✅ Se viu a quantidade aumentar de 10 para 15, está funcionando!

---

## 🎉 Parabéns! Sistema Pronto!

Se conseguiu chegar até aqui, seu sistema está 100% funcional!

---

## ⚠️ Se Algo Deu Errado

### Erro: "SQLSTATE[HY000]: General error: 1 no such table"

```bash
# Execute:
php artisan migrate:fresh
# (Isso limpa e recria todas as tabelas)
```

**⚠️ Cuidado**: Isso vai deletar TODOS os dados existentes!

### Erro: "Class not found"

```bash
# Execute:
composer dump-autoload
php artisan cache:clear
```

### Erro: ".env not found"

```bash
# Execute:
cp .env.example .env
php artisan key:generate
```

### Erro: "No such table"

```bash
# Verifique o banco de dados:
php artisan migrate --refresh
```

---

## 📝 Arquivos de Suporte

Se tiver dúvidas, leia:

1. **QUICK_START.md** - Como usar (5 min)
2. **README_PRODUTOS.md** - Informações (10 min)
3. **GUIA_PRODUTOS_E_ENTRADAS.md** - Documentação (20 min)

---

## 💡 Dicas

1. **Sempre mantenha o servidor rodando**: `php artisan serve`
2. **Use SKUs consistentes**: Ex: PROD-TIPO-001
3. **Defina bom ressuprimento**: Baseado na sua demanda
4. **Confira regularmente**: Menu → Entradas

---

## ✅ Checklist Final

- [ ] Requisitos verificados (PHP 8.1+, Composer)
- [ ] Arquivos criados (Models, Controllers, Views)
- [ ] Migrations executadas
- [ ] Cache limpo
- [ ] Servidor iniciado
- [ ] Login realizado
- [ ] Produto cadastrado
- [ ] Entrada registrada

Se todos esses itens estão marcados, você está pronto! 🚀

---

## 📞 Próximas Ações

1. Explore o sistema
2. Cadastre mais produtos
3. Registre entradas
4. Verifique o histórico
5. Aproveite!

---

**Versão**: 1.0
**Última Atualização**: Janeiro 2024
**Status**: ✅ Pronto para Uso
