# 🚀 Quick Start - Comece Agora!

## Em 5 minutos, seu sistema de produtos estará funcionando

### Passo 1: Executar as Migrações (1 min)
```bash
php artisan migrate
```

✅ **Pronto!** As tabelas foram criadas no banco de dados.

---

### Passo 2: Fazer Login (1 min)
1. Acesse seu site: `http://seu-dominio.com/login`
2. Faça login com suas credenciais

---

### Passo 3: Acessar o Menu de Produtos (1 min)
1. Você verá a barra lateral esquerda
2. Clique em "Produtos" 
3. **Voilà!** Você está na tela de consulta

---

### Passo 4: Cadastrar Seu Primeiro Produto (2 min)

#### Na tela de Produtos:
1. Clique no botão **"+ Novo Produto"** (canto superior direito)
2. Preencha os dados:
   - **Nome**: Ex: "Notebook Dell"
   - **SKU**: Ex: "NB-DELL-001" (código único)
   - **Preço Unitário**: Ex: "2500.00"
   - **Quantidade**: Ex: "10"
   - **Nível de Ressuprimento**: Ex: "5" (avisa quando chegar aqui)
   - **Descrição**: Opcional

3. Clique em **"Cadastrar Produto"** ✅

---

### Passo 5: Registrar uma Entrada (1 min)

#### No detalhe do produto:
1. Na lista, clique no produto para ver detalhes
2. No painel verde **"Registrar Entrada"** (lado direito):
   - **Quantidade**: Digite quantas unidades entraram
   - **Observações**: Opcional (ex: "Lote 123")
3. Clique em **"Confirmar Entrada"** ✅

---

## 📍 Onde Encontrar Cada Tela

| O Que Fazer | Onde Encontrar |
|---|---|
| Ver todos os produtos | Menu → **Produtos** |
| Cadastrar novo | Menu → Produtos → **+ Novo Produto** |
| Ver detalhes | Menu → Produtos → Click no produto |
| Editar | No detalhe ou na lista → ✏️ |
| Deletar | No detalhe ou na lista → 🗑️ |
| Registrar entrada | Detalhe do produto → Painel verde |
| Ver histórico de entradas | Menu → **Entradas** |

---

## 🎯 Casos de Uso Rápidos

### Cenário 1: Você recebeu estoque novo
1. Vá em: Menu → **Entradas**
2. Clique em um produto
3. Use o **Registrar Entrada** para adicionar qty
4. ✅ Pronto!

### Cenário 2: Fazer uma busca
1. Menu → **Produtos**
2. Na barra de busca, digite o nome ou SKU
3. ✅ Resultados aparecem automaticamente

### Cenário 3: Verificar o status do estoque
1. Menu → **Produtos**
2. Olhe a coluna "Quantidade"
   - 🟢 Verde = Tudo bem
   - 🟡 Amarelo = Atenção
   - 🔴 Vermelho = Comprar mais!

---

## ❓ Dúvidas Frequentes

### P: Como saber se estou abaixo do limite?
**R:** Veja a cor da quantidade:
- 🔴 Vermelho = Está abaixo (compre logo!)
- 🟡 Amarelo = Está perto (prepare-se)
- 🟢 Verde = Está tranquilo

### P: Como busco um produto?
**R:** Na página de Produtos, use a barra de busca por nome ou SKU

### P: Posso editar estoque direto?
**R:** Não, mas você pode usar "Registrar Entrada" para adicionar quantidade

### P: Posso deletar um produto?
**R:** Sim, mas o sistema vai pedir confirmação e vai apagar o histórico também

### P: Onde vejo o histórico de movimentações?
**R:** Menu → **Entradas** (mostra todas as chegadas)

---

## ⚡ Dicas Profissionais

1. **Use SKUs consistentes**: Ex: "PROD-TIPO-CODIGO"
2. **Defina bom nível de ressuprimento**: Baseado na sua demanda
3. **Adicione observações**: Lote, fornecedor, etc (em Entradas)
4. **Confira regularmente**: Menu → Entradas → veja o que chegou

---

## 🔧 Se Algo Não Funcionar

❌ **Erro: "Table not found"**
- Execute: `php artisan migrate`

❌ **Não vejo o menu de Produtos**
- Recarregue a página (F5)
- Faça logout e login novamente

❌ **Não consigo cadastrar**
- Verifique os campos obrigatórios (com *)
- SKU não pode ser repetido

---

## 📞 Suporte Rápido

- Documentação completa: `GUIA_PRODUTOS_E_ENTRADAS.md`
- Resumo técnico: `IMPLEMENTACAO_RESUMO.md`

---

## ✨ Enjoy! 

Seu sistema de controle de estoque já está 100% funcional!

**Próxima ação**: Cadastre seus primeiros produtos 🚀

---

*Última atualização: Janeiro 2024*
