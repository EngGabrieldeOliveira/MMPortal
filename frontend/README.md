# MMPortal Frontend

SPA React 19 + Vite para o MMPortal.

## Configuração

Crie `frontend/.env` a partir de `.env.example` e defina:

```env
VITE_API_URL=http://127.0.0.1:8000/api
```

O Axios usa exclusivamente `VITE_API_URL` e envia o Bearer Token do Sanctum armazenado após o login. Respostas 401 removem a sessão local e redirecionam para o login.

## Comandos

```bash
npm install
npm run dev
npm run lint
npm run build
```

## Tailwind 4

O projeto mantém Tailwind 4 porque `src/index.css` importa `tailwindcss`. O plugin oficial `@tailwindcss/vite` está configurado em `vite.config.ts`; os estilos visuais existentes continuam em CSS próprio.
