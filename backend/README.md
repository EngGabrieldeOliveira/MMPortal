# MMPortal API

API Laravel 13 para o MMPortal.

## Autenticação

A API usa Laravel Sanctum com Bearer Tokens. Faça `POST /api/auth/login` e envie o token retornado em `Authorization: Bearer {token}`. Rotas privadas usam `auth:sanctum`.

- `GET /api/auth/me`: usuário autenticado.
- `POST /api/auth/logout`: revoga somente o token enviado.
- `POST /api/auth/logout-all`: revoga todos os tokens do usuário.

Todas as respostas JSON seguem `{ "data": {}, "message": "", "errors": {} }`.

## Comandos

```bash
composer install
php artisan migrate
php artisan db:seed
composer analyse
php artisan test
vendor/bin/pint --test
```

Os perfis disponíveis são: `administrador`, `diretoria`, `comercial`, `engenharia`, `producao`, `compras`, `financeiro`, `rh` e `obras`.
