---
name: laravel-php-architect-review
description: 'Use when reviewing, refactoring, or auditing Laravel/PHP code for production: security (auth/authz, mass assignment, uploads, SQLi, XSS, CSRF), Laravel best practices (Form Requests, policies, DI, services), performance (N+1, eager loading, caching/queues), and database quality (migrations, FKs, indexes).'
argument-hint: 'Provide file paths, expected behavior, roles/permissions, and constraints (preserve business logic unless flawed).'
---

# Laravel/PHP Architect Review (produção)

## Objetivo
Fazer review, diagnosticar riscos e produzir refatorações incrementais e seguras em aplicações Laravel/PHP, com foco em:
- Segurança (authn/authz, input validation, uploads, dados sensíveis)
- Performance (N+1, consultas, memória, jobs)
- Qualidade (SOLID, Clean Code, camadas, duplicação)
- Banco (migrations, índices, constraints, integridade)

## Quando usar
Use este skill quando o usuário pedir (ou implicar) qualquer um destes cenários:
- “review”, “auditar”, “refatorar”, “melhorar”, “hardening” de código Laravel/PHP
- Bugs em endpoints, controllers, jobs/queues, observers, policies
- Problemas de performance (lentidão, muitas queries, N+1, timeouts)
- Dúvidas de arquitetura (service layer, separation of concerns, testes)

Não use quando:
- A tarefa for puramente frontend/CSS
- O usuário só quiser um snippet isolado sem contexto de app

## Entradas mínimas (pergunte se faltarem)
1. Caminhos/arquivos envolvidos (ex.: `app/Http/Controllers/...`, `routes/web.php`, migrations)
2. Comportamento esperado vs atual (incluindo exemplos de payload)
3. Regras de negócio que NÃO podem mudar
4. Papel do usuário/permite? (papéis, permissões, escopo do tenant)
5. Volume esperado (linhas, QPS, tamanho de arquivos) quando performance importar

## Workflow (multi-pass)
### Passo 1 — Mapear o “entrypoint” e o fluxo
- Identifique por onde entra (route → controller → service/model/job → response).
- Liste dependências imediatas: Form Requests, Policies/Gates, Models, Observers, Jobs, Events.
- Se houver side effects (e-mail, arquivos, estoque), marque como **alto risco** e priorize testes.

### Passo 2 — Security pass (primeiro)
Procure e trate prioritariamente:
- **Authorization**: ações sem `authorize()`/Policy/Gate; checagens em controller “na mão”
- **Authentication**: endpoints assumindo usuário autenticado sem middleware
- **Mass assignment**: `Model::create($request->all())`, `update($request->all())`
- **Validação fraca**: `Request` sem Form Request; uso de `$request->input()` sem `validated()`
- **Uploads**: sem validação de `mimes`, `max`, sem `Storage`, path traversal, nome não sanitizado
- **SQL injection**: concatenação em `DB::raw()`, `whereRaw()` com input
- **XSS**: output em Blade sem escaping ou HTML vindo do usuário
- **Dados sensíveis**: logs com payload completo; exceções expondo detalhes

Padrões recomendados (idiomático Laravel):
- Preferir **Form Requests** + `$request->validated()`
- Preferir **Policies** (`$this->authorize('update', $model)`) e **route-model binding**
- Encapsular operações críticas em **transações** (`DB::transaction`) quando houver múltiplas escritas
- Para uploads: `$request->file('...')->storePublicly(...)`/`store(...)` em disco configurado

### Passo 3 — Laravel best practices pass
Checklist:
- Controllers pequenos: orquestram; não implementam regra complexa
- Extraia regra para **Service/Action** (ex.: `app/Services` ou `app/Actions`) com DI
- Use **API Resources** para serialização consistente
- Use **Middleware** para cross-cutting (auth, rate limit, tenant)
- Use **Exceptions** específicas + Handler para mapear erros previsivelmente
- Use **Events/Listeners** para acoplamento baixo quando fizer sentido

### Passo 4 — Performance pass
Procure:
- **N+1**: loops acessando relações sem eager load
- Queries repetidas: `Model::find(...)` dentro de loops
- Falta de paginação: `->get()` em listas grandes
- Falta de índices: filtros frequentes sem index
- Processamento pesado síncrono: mover para Job/Queue

Padrões:
- Eager load: `Model::query()->with(['relA', 'relB'])`
- Paginação: `->paginate()` / `->simplePaginate()`
- Selecionar colunas: `->select(['id','name'])` quando aplicável
- Cache: `Cache::remember()` para leituras caras e estáveis

### Passo 5 — Database pass
Em migrations e modelagem:
- FKs e índices onde fazem sentido (principalmente colunas de busca e joins)
- Consistência de tipos (unsignedBigInteger para chaves)
- Constraints e defaults explícitos
- Evitar colunas “polimórficas” sem necessidade (impacta integridade e performance)

### Passo 6 — Refatoração incremental (sem quebrar negócio)
- Primeiro, corrija vulnerabilidades e bugs lógicos.
- Depois, refatore para legibilidade e separação (controller → service/action).
- Evite reformatar ou renomear em massa sem ganho real.

### Passo 7 — Verificação (o mínimo confiável)
- Rodar testes existentes (ex.: `php artisan test` ou `vendor/bin/phpunit`).
- Se não houver testes, ao menos validar manualmente o endpoint/fluxo alterado.
- Se a mudança altera query/performance, revisar query logs e eager loading.

## Red flags (interrompa e trate antes)
- `->all()`/`->only()` usado direto em `create/update` sem whitelist e sem `fillable`
- Ausência de `authorize()` em ações mutáveis (create/update/delete)
- Upload gravando em path vindo do usuário
- `whereRaw()`/`DB::raw()` com input
- Lista grande sem paginação

## Formato de saída (OBRIGATÓRIO)
Para cada ponto relevante (ou por “tema”), responda sempre nesta estrutura:
1. **Problema detectado**
2. **Por que é um problema**
3. **Solução recomendada**
4. **Código refatorado**
5. **Melhoria arquitetural opcional**

## Exemplo mínimo (padrão de correção de mass assignment)
1. **Problema detectado**: `User::create($request->all())`.
2. **Por que é um problema**: permite mass assignment e grava campos não intencionais.
3. **Solução recomendada**: Form Request + `$request->validated()` + `$fillable`.
4. **Código refatorado**:

```php
public function store(StoreUserRequest $request)
{
    $this->authorize('create', User::class);

    $user = User::create($request->validated());

    return new UserResource($user);
}
```

5. **Melhoria arquitetural opcional**: extrair criação para `CreateUserAction` e isolar side effects (e-mail) via Event.
