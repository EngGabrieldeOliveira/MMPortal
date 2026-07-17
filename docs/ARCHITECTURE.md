# Arquitetura do MMPortal

## Visão geral

O MMPortal utiliza React/Vite separado de uma API Laravel 13. A regra de negócio reside no backend com Eloquent, Form Requests, Services e controllers por recurso.

## Autenticação e autorização

Laravel Sanctum emite Bearer Tokens para o frontend. As rotas privadas usam `auth:sanctum`; `request()->user()` e `Auth::user()` identificam o usuário autenticado. O logout remove apenas o token atual e `tokens()->delete()` permite revogação global.

`UserRole` contém os perfis iniciais. A infraestrutura RBAC inclui `permissions`, `role_permissions` e `user_permissions`; permissões individuais podem permitir ou negar uma permissão recebida do perfil. O middleware `permission:{chave}` e o Gate `permission` estão prontos para adoção incremental, sem restringir funcionalidades existentes.

## Auditoria e eventos

`audit_logs` registra criação, alteração, exclusão lógica e restauração dos registros críticos. Cada linha contém usuário, IP, User Agent, request ID, módulo, ação, valores anteriores e novos e referência polimórfica ao registro.

`administrative_events` registra login, logout, tentativa inválida, upload e exceção interna. `AdministrativeEventType` também normaliza os eventos de alteração de permissões, configuração e exportação para os módulos que forem criados futuramente. O canal `administrative` também grava logs diários em arquivo.

UUID é utilizado nesses registros transversais para correlação e eventual exposição segura. As entidades de negócio preservam IDs numéricos e códigos existentes (`CLI`, `SOL`, `ORC`, `PED`, `OS`).

## Dados e exclusão

Clientes, solicitações, orçamentos, pedidos, ordens de serviço e anexos usam Soft Delete. Nenhuma rota atual executa exclusão física ou remove o arquivo armazenado. Consultas Eloquent normais ocultam registros removidos e preservam o histórico.

## Contrato HTTP

Respostas JSON seguem:

```json
{ "data": {}, "message": "", "errors": {} }
```

Validação, autenticação, autorização, registros ausentes e erros internos seguem o mesmo formato. Arquivos visualizados ou baixados retornam conteúdo binário por definição.
