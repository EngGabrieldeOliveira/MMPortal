# MMPortal

Portal operacional para o ciclo comercial e produtivo: solicitações, orçamentos, pedidos e ordens de serviço.

## Estrutura

- `backend/`: API Laravel 13 com Sanctum e Eloquent.
- `frontend/`: SPA React/Vite.
- `docs/ARCHITECTURE.md`: decisões técnicas, autenticação e autorização.

## Início rápido

1. Configure `backend/.env` com o banco de dados.
2. Execute `composer install`, `php artisan migrate` e `php artisan db:seed` dentro de `backend/`.
3. Configure `frontend/.env` com `VITE_API_URL` apontando para a API, por exemplo `http://127.0.0.1:8000/api`.
4. Em `frontend/`, execute `npm install` e `npm run dev`.

Os comandos de qualidade estão documentados nos READMEs de cada aplicação.
