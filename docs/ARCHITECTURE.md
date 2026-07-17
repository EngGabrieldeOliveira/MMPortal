# Arquitetura do MMPortal

## Visão geral

O MMPortal usa uma SPA React separada e uma API Laravel 13. A regra de negócio permanece no backend com Eloquent, Form Requests, Services e controllers por recurso.

## Autenticação

Laravel Sanctum emite tokens pessoais para o cliente `frontend`. As rotas privadas usam `auth:sanctum`; portanto `request()->user()` e `Auth::user()` identificam o usuário autenticado. O logout remove apenas `currentAccessToken()`, enquanto `tokens()->delete()` oferece revogação global futura.

Tokens próprios, middleware `api.token`, `users.api_token` e a tabela `api_tokens` foram removidos. Sessões antigas não são migráveis porque o sistema anterior armazenava somente hashes; usuários devem autenticar novamente após a migration.

## Autorização

`App\Enums\UserRole` centraliza os papéis iniciais: administrador, diretoria, comercial, engenharia, produção, compras, financeiro, RH e obras. `AppServiceProvider` registra Gates por domínio. Não há pacote de permissões externo: o modelo atual é suficiente para a primeira etapa e poderá evoluir para Policies por recurso sem alterar a autenticação.

## Fluxo comercial

- `SolicitacaoController`: listagem, cadastro, leitura e atualização.
- `SolicitacaoAnexoController`: anexar, renomear, visualizar, baixar e remover documentos.
- `OrcamentoController`: orçamento direto, conversão de solicitação, envio e decisão.
- `OrdemServicoController`: criação de OS e atualização de etapas.

As transações e transições mais sensíveis estão em `OrcamentoService`, `OrdemServicoService` e `StatusHistoryService`. Não há Repository genérico; os controllers usam Eloquent diretamente.

## Contrato HTTP

Respostas JSON usam sempre:

```json
{ "data": {}, "message": "", "errors": {} }
```

Erros de validação e autenticação também respeitam esse formato. Arquivos visualizados ou baixados são exceções deliberadas, pois retornam conteúdo binário.
