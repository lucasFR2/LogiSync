# 🧪 TESTE SEU SISTEMA

## Comandos para verificar se tudo está funcionando

---

## ✅ PASSO 1: Verificar Estrutura (sem executar)

```bash
# Verificar se os Models existem
ls app/Models/ | grep -E "Product|Inventory"
# Deve listar: Product.php, Inventory.php

# Verificar se o Controller existe
ls app/Http/Controllers/ | grep Product
# Deve listar: ProductController.php

# Verificar se as Migrations existem
ls database/migrations/ | grep 2024_01_15
# Deve listar: 2 arquivos
```

---

## ✅ PASSO 2: Executar as Migrações (IMPORTANTE!)

```bash
php artisan migrate
```

✅ **Se viu**: "Migrated successfully" = OK!

❌ **Se viu erro**: Verifique seu .env (DB_CONNECTION, DB_HOST, etc)

---

## ✅ PASSO 3: Testar com Artisan Tinker

```bash
php artisan tinker

# Dentro do tinker, execute:
>>> $product = new App\Models\Product();
>>> $product->name = "Teste";
>>> $product->sku = "TEST-001";
>>> $product->unit_price = 100;
>>> $product->quantity = 10;
>>> $product->reorder_level = 5;
>>> $product->save();
>>> exit

# Se não deu erro, está funcionando!
```

---

## ✅ PASSO 4: Verificar Rotas

```bash
php artisan route:list | grep product
```

Deve listar:
```
GET|HEAD  /products..................... products.index
GET|HEAD  /products/create.............. products.create
POST      /products..................... products.store
GET|HEAD  /products/{product}........... products.show
GET|HEAD  /products/{product}/edit...... products.edit
PUT|PATCH /products/{product}........... products.update
DELETE    /products/{product}........... products.destroy
```

---

## ✅ PASSO 5: Iniciar o Servidor

```bash
php artisan serve
```

Você verá:
```
INFO  Server running on [http://127.0.0.1:8000]
```

---

## ✅ PASSO 6: Acessar no Navegador

Abra seu navegador:

```
http://localhost:8000/login
```

1. Faça login
2. Clique em "Produtos" no menu
3. Você deve ver a página de produtos

✅ Se viu a página = Sistema OK!

---

## ✅ PASSO 7: Criar um Produto

1. Clique em "+ Novo Produto"
2. Preencha:
   - Nome: "Notebook"
   - SKU: "NB-001"
   - Preço: "2500"
   - Quantidade: "10"
   - Ressuprimento: "5"
3. Clique "Cadastrar"

✅ Se viu mensagem verde = OK!

---

## ✅ PASSO 8: Registrar Entrada

1. Na lista, clique no produto
2. Direita: "Registrar Entrada"
3. Digite: "5"
4. Clique "Confirmar"

✅ Se a quantidade passou de 10 para 15 = OK!

---

## ✅ PASSO 9: Verificar Histórico

1. Clique em "Entradas" no menu
2. Você deve ver a entrada que registrou

✅ Se apareceu sua entrada = OK!

---

## ✅ PASSO 10: Editar e Deletar

### Editar:
1. Na lista, clique no ícone de lápis
2. Altere algum campo
3. Clique "Atualizar"

✅ Se viu mensagem de sucesso = OK!

### Deletar:
1. Na lista, clique no ícone de lixeira
2. Confirme
3. Produto deve desaparecer

✅ Se desapareceu = OK!

---

## 📊 CHECKLIST DE TESTES

- [ ] Migrations executadas sem erro
- [ ] Artisan tinker criou produto
- [ ] Rotas listaram corretamente
- [ ] Servidor iniciou
- [ ] Login funcionou
- [ ] Página de produtos carregou
- [ ] Cadastrou novo produto
- [ ] Registrou entrada
- [ ] Histórico appareceu
- [ ] Editou produto
- [ ] Deletou produto

**Se todos marcados** = Sistema 100% OK! ✅

---

## 🐛 Se Algo Deu Errado

### Erro na Migração
```bash
php artisan migrate:fresh
# Isso recriar as tabelas
```

### Produto não aparece na lista
```bash
# Verifique o banco:
php artisan tinker
>>> App\Models\Product::all();
```

### 404 em /products
```bash
php artisan route:clear
php artisan cache:clear
```

### Erro "Table not found"
```bash
php artisan migrate
# E confirme que rodou sem erro
```

---

## ✨ Sistema Testado!

Se passou por todos os 10 passos sem erro:

✅ Model funcionando
✅ Controller funcionando
✅ View funcionando
✅ Banco de dados OK
✅ Rotas OK
✅ CRUD funcionando

**SEU SISTEMA ESTÁ 100% FUNCIONAL!** 🚀

---

## 📝 Logs (se precisar debugar)

```bash
# Ver últimos logs
tail -f storage/logs/laravel.log
```

---

**Pronto!** Seu sistema está testado e validado! 🎉
