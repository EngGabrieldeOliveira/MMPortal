# MMPortal

ERP operacional da Marques Metais para solicitações, orçamentos, pedidos e ordens de serviço.

## Estrutura

- `backend/`: API Laravel 13, Sanctum, Eloquent e infraestrutura corporativa.
- `frontend/`: SPA React/Vite.
- `docs/`: arquitetura, decisões, sprints, changelog e revisão técnica.

## Início rápido

1. Configure `backend/.env` com o banco de dados.
2. Em `backend/`, execute `composer install`, `php artisan migrate` e `php artisan db:seed`.
3. Configure `frontend/.env` com `VITE_API_URL` apontando para a API.
4. Em `frontend/`, execute `npm install` e `npm run dev`.

## Infraestrutura corporativa

A Sprint 04 adiciona auditoria persistente, eventos administrativos, Soft Delete, RBAC extensível e tratamento global de exceções. Consulte [arquitetura](docs/ARCHITECTURE.md), [decisões](docs/DECISIONS.md), [sprints](docs/SPRINTS.md) e [revisão técnica](docs/CTO_REVIEW.md).
