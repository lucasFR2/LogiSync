# LogiSync WMS - Guia de Uso

## 📋 Telas Implementadas

Este projeto conta com as seguintes três telas principais conforme solicitado:

### 1. **Consulta de Produtos** (`/products`)
- **Descrição**: Lista todos os produtos cadastrados no sistema
- **Funcionalidades**:
  - Listagem paginada de produtos
  - Busca por nome ou SKU
  - Status visual do estoque (Em estoque, Atenção, Abaixo do limite)
  - Ações rápidas: Ver Detalhes, Editar, Deletar
  - Exibição de quantidade, preço unitário e nível de ressuprimento
  
**Acesso**: Menu lateral → Produtos

---

### 2. **Cadastro de Produtos** (`/products/create`)
- **Descrição**: Formulário para cadastrar novos produtos
- **Campos Obrigatórios**:
  - Nome do Produto
  - SKU (código único do produto)
  - Preço Unitário (R$)
  - Quantidade em Estoque
  - Nível de Ressuprimento
- **Campos Opcionais**:
  - Descrição do produto
  
**Acesso**: Na tela de Consulta de Produtos → Botão "Novo Produto"

---

### 3. **Controle de Entradas** (`/inventory`)
- **Descrição**: Controle de todas as movimentações de entrada de estoque
- **Funcionalidades**:
  - Histórico completo de entradas
  - Estatísticas: Total de entradas, Este mês, Hoje, Produtos movimentados
  - Listagem com filtro de produto, quantidade e observações
  - Acesso rápido aos detalhes do produto
  
**Acesso**: Menu lateral → Entradas

---

## 🚀 Como Configurar e Usar

### Pré-requisitos
- PHP 8.1+
- Laravel 11.x
- MySQL/MariaDB ou SQLite
- Composer

### 1. Executar as Migrações

```bash
php artisan migrate
```

Isso criará as seguintes tabelas:
- `products` - Armazena dados dos produtos
- `inventories` - Armazena histórico de entradas

### 2. Acessar o Sistema

1. Faça login no sistema com suas credenciais
2. No menu lateral, clique em "Produtos" para começar

---

## 📱 Funcionalidades Principais

### Gerenciamento de Produtos

#### Cadastrar Produto
1. Acesse a tela de Consulta de Produtos
2. Clique no botão "Novo Produto"
3. Preencha todos os campos obrigatórios
4. Clique em "Cadastrar Produto"

#### Editar Produto
1. Na lista de produtos, clique no ícone de lápis (✏️)
2. Altere os dados desejados
3. Clique em "Atualizar Produto"

#### Deletar Produto
1. Na lista de produtos ou na tela de detalhes, clique no ícone de lixeira (🗑️)
2. Confirme a exclusão

#### Visualizar Detalhes
1. Na lista, clique no nome do produto ou no ícone de olho (👁️)
2. Aparecerão:
   - Informações completas do produto
   - Status do estoque
   - Formulário para registrar entrada
   - Histórico de entradas

### Controle de Entradas

#### Registrar Entrada
1. Acesse a tela de detalhes do produto
2. No painel "Registrar Entrada" (lado direito):
   - Informe a quantidade
   - (Opcional) Adicione observações
3. Clique em "Confirmar Entrada"
4. A quantidade em estoque será atualizada automaticamente

#### Visualizar Histórico
1. Na tela de detalhes do produto, veja as entradas registradas
2. Ou acesse a seção "Controle de Entradas" no menu para ver todas as movimentações

---

## 📊 Indicadores de Status

O sistema utiliza cores para indicar o status do estoque:

- **🟢 Verde (Em estoque)**: Quantidade acima de 1.5x o nível de ressuprimento
- **🟡 Amarelo (Atenção)**: Quantidade entre o nível de ressuprimento e 1.5x
- **🔴 Vermelho (Abaixo do limite)**: Quantidade menor que o nível de ressuprimento

---

## 🏗️ Arquitetura

### Models
- `Product` - Modelo do produto
- `Inventory` - Modelo de movimentação de estoque

### Controllers
- `ProductController` - Controlador responsável pelos produtos e inventário

### Views
- `products/index.blade.php` - Consulta de produtos
- `products/create.blade.php` - Cadastro de produtos
- `products/edit.blade.php` - Edição de produtos
- `products/show.blade.php` - Detalhes do produto + Controle de entradas
- `inventory/index.blade.php` - Histórico de entradas

### Rotas
```
GET  /products              - Listar produtos
GET  /products/create       - Formulário de criação
POST /products              - Salvar novo produto
GET  /products/{id}         - Detalhes do produto
GET  /products/{id}/edit    - Formulário de edição
PUT  /products/{id}         - Atualizar produto
DELETE /products/{id}       - Deletar produto
POST /products/{id}/add-inventory - Registrar entrada
GET  /inventory             - Listagem de entradas
```

---

## 🎨 Padrão Visual

O projeto segue o padrão de design utilizado no dashboard:
- **Framework CSS**: Tailwind CSS
- **Ícones**: Font Awesome 6.0.0
- **Layout**: Sidebar + Conteúdo principal
- **Paleta de cores**: 
  - Azul (#3b82f6) - Primária
  - Verde (#10b981) - Sucesso
  - Vermelho (#ef4444) - Alertas

---

## 🔧 Dicas de Personalização

### Alterar os estilos
Edite as classes Tailwind CSS nos arquivos `.blade.php` conforme necessário.

### Adicionar novos campos
1. Adicione o campo no migration
2. Adicione no `$fillable` do Model
3. Adicione o input HTML na view

### Alterar validações
Edite as requisições no `ProductController` no método `store()` e `update()`

---

## 📝 Notas Importantes

- Todos os produtos devem ter um SKU único
- O nível de ressuprimento é usado apenas para indicar alerta
- As entradas criam um histórico que não pode ser deletado
- Deletar um produto também remove seu histórico de entradas
- O sistema valida automaticamente campos vazios e SKU duplicado

---

## ❓ Troubleshooting

### Erro: "SQLSTATE[HY000]: General error: 1 no such table: products"
**Solução**: Execute `php artisan migrate`

### Botões não funcionam
**Solução**: Verifique se os links nas rotas estão corretos em `routes/web.php`

### Estilos não aparecem
**Solução**: Aguarde o Tailwind compilar os estilos ou execute `npm run dev`

---

## 📞 Suporte

Para dúvidas ou problemas, verifique os logs em `storage/logs/` ou execute:
```bash
php artisan tinker
```

Enjoy! 🚀
