# Desafio Vox - Kanban

Aplicação web de Kanban estilo Trello com:

- autenticação (registro/login/logout);
- quadros, categorias (colunas) e tasks;
- CRUD via API REST;
- drag-and-drop com persistência de ordenação;
- frontend em Blade + Bootstrap + jQuery/AJAX.

## Stack

- PHP `^8.2`
- Laravel `12.x`
- PostgreSQL
- Bootstrap 5
- jQuery
- Vite (assets compilados)

---

## Como rodar (recomendado) — Docker (modo recrutador)

### Requisitos
- Docker
- Docker Compose

### Passo a passo

1. Na raiz do projeto, suba os containers:

```bash
docker compose up --build
```

2. Acesse a aplicação:

- http://localhost:8000

### O que acontece automaticamente ao subir
Ao iniciar o container da aplicação, ele executa:

- `composer install`
- cria `.env` a partir de `.env.example` (se não existir)
- gera `APP_KEY` (se necessário)
- roda `php artisan migrate`
- sobe o servidor em `0.0.0.0:8000`

> Observação: os assets do frontend **já são buildados no Docker** (não precisa rodar `npm run dev`).

### Parar / resetar (opcional)

Parar:
```bash
docker compose down
```

Reset completo (apaga o banco):
```bash
docker compose down -v
docker compose up --build
```

---

## Como rodar (alternativo) — Sem Docker

### Requisitos
- PHP 8.2+ com extensões comuns do Laravel
- Composer
- Node.js 20+ e npm
- PostgreSQL 14+

### Passo a passo

1. Instale dependências PHP e JS:

```bash
composer install
npm install
```

2. Crie e configure o arquivo de ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

3. Ajuste as variáveis de banco no `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kanban
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

4. Rode as migrations:

```bash
php artisan migrate
```

5. Gere o build do frontend (recomendado para simular produção):

```bash
npm run build
```

6. Suba a aplicação:

```bash
php artisan serve
```

A aplicação ficará em `http://127.0.0.1:8000`.

> Se preferir modo desenvolvimento (hot reload), use:
> `npm run dev` (em outro terminal)

---

## Fluxo funcional

1. Acesse `/` e crie uma conta.
2. Crie um quadro (modal AJAX).
3. Crie categorias (colunas) no quadro.
4. Crie tasks em cada categoria.
5. Arraste as tasks entre categorias para mover e reordenar.

---

## Endpoints REST

Todas as rotas abaixo exigem usuário autenticado (sessão web):

- `GET /api/boards`
- `POST /api/boards`
- `PATCH|PUT /api/boards/{board}`
- `DELETE /api/boards/{board}`
- `GET /api/boards/{board}/columns`
- `POST /api/boards/{board}/columns`
- `PATCH|PUT /api/columns/{column}`
- `DELETE /api/columns/{column}`
- `GET /api/columns/{column}/tasks`
- `POST /api/columns/{column}/tasks`
- `PATCH|PUT /api/tasks/{task}`
- `DELETE /api/tasks/{task}`
- `PATCH /api/tasks/{task}/move`

Endpoint legado mantido por compatibilidade:

- `PATCH /api/tasks/reorder`

---

## Testes

```bash
php artisan test
```

Inclui testes de:

- proteção de rota autenticada;
- criação de board por API;
- isolamento de acesso entre usuários;
- movimentação/ordenação de tasks.
