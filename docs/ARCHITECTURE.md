# Arquitetura do MMPortal

## UI reutilizável

O frontend concentra padrões de tela em `components/layout`, `components/forms` e `components/navigation`. `PageHeader`, `FormActions` e `SectionNavigation` não carregam regras de negócio e podem ser adotados gradualmente.

## Visão geral

O MMPortal utiliza React/Vite separado de uma API Laravel 13. A regra de negócio reside no backend com Eloquent, Form Requests, Services e controllers por recurso.

## Autenticação e autorização

Laravel Sanctum emite Bearer Tokens para o frontend. As rotas privadas usam `auth:sanctum`; `request()->user()` e `Auth::user()` identificam o usuário autenticado. O logout remove apenas o token atual e `tokens()->delete()` permite revogação global.

`UserRole` contém os perfis iniciais. A infraestrutura RBAC inclui `permissions`, `role_permissions` e `user_permissions`; permissões individuais podem permitir ou negar uma permissão recebida do perfil. O middleware `permission:{chave}` e o Gate `permission` estão prontos para adoção incremental, sem restringir funcionalidades existentes.

## Auditoria, eventos e exclusão

`audit_logs` registra criação, alteração, exclusão lógica e restauração dos registros críticos. `administrative_events` registra eventos de segurança e operação. Clientes, solicitações, orçamentos, pedidos, ordens de serviço e anexos usam Soft Delete; nenhum endpoint atual remove registros comerciais fisicamente.

UUID é utilizado em auditoria e eventos para correlação segura. As entidades de negócio preservam IDs numéricos e códigos existentes (`CLI`, `SOL`, `ORC`, `PED`, `OS`).

## Evolução planejada do módulo de Clientes

O cadastro atual será evoluído sem troca de chave nem ruptura das relações existentes. A arquitetura aprovada inclui classificações múltiplas (cliente, fornecedor e transportadora), filiais como cadastros próprios vinculados à matriz, endereço inicial apenas de cobrança, contatos múltiplos, responsáveis internos múltiplos, documentos ilimitados e parâmetros financeiros por cadastro.

O desenho de domínio, API, UX, wireframes, riscos e integrações encontra-se em [CLIENT_MODULE.md](CLIENT_MODULE.md). Nenhuma dessas tabelas, rotas, regras ou telas foi implementada na Sprint 06A.

## Contrato HTTP

Respostas JSON seguem:

```json
{ "data": {}, "message": "", "errors": {} }
```

Validação, autenticação, autorização, registros ausentes e erros internos seguem o mesmo formato. Arquivos visualizados ou baixados retornam conteúdo binário por definição.
