# Sprints

## Padronização visual global das telas internas

- Criados `PageHeader`, `SectionNavigation` e `FormActions` para uso gradual nas telas equivalentes.
- Variantes de botão foram alinhadas a ações neutras, de atenção, sucesso e perigo.

## Sprint 04 — Infraestrutura Corporativa

Objetivo: fortalecer segurança, rastreabilidade e manutenção sem criar telas ou alterar UX.

Entregas: auditoria, eventos administrativos, Soft Delete, RBAC extensível, exceções padronizadas, request IDs, testes de infraestrutura e documentação técnica.

## Sprint 05 — Cockpit Executivo

Entregas: interface executiva, integração com `GET /api/dashboard`, estados de carregamento e erro e componentes reutilizáveis. O Cockpit consome o contrato existente da API.

## Sprint 06A — Arquitetura do Módulo de Clientes

Objetivo: definir a evolução do cadastro para pessoa física e jurídica, classificações múltiplas, matriz e filiais independentes, contatos, cobrança, responsáveis internos, documentos, parâmetros financeiros, UX e integrações futuras.

Entregas: documentação arquitetural e ADR. Não foram criadas migrations, endpoints, regras de negócio ou telas nesta Sprint.

## Sprint 06B — Backend do Módulo de Clientes

Entregas: schema relacional, API REST, validações, Resources, Service transacional, Policy, permissões, auditoria, factories, seeders e testes. Não houve alteração de frontend.

## Sprint 06C — Frontend e Integração do Módulo de Clientes

### Ajustes de homologação

- Classificações foram incorporadas a Dados Gerais, o CEP passou a ser consultado automaticamente com alternativa manual e as condições comerciais aceitam texto livre.
- O limite de faturamento agora pode ser explicitamente ilimitado; a inativação/reativação usa endpoint próprio e permanece separada da exclusão lógica.
- Os detalhes do cliente passaram a uma página contínua, com navegação por âncoras entre os blocos de informação.
- Ajuste final: responsável interno único com seleção por nome, tratamento específico para pessoa física e layout executivo de detalhes com estados vazios reais.

Entregas: listagem, cadastro, edição e detalhes integrados à API real; busca, filtros na URL, paginação, ordenação de servidor, validação de formulário, contatos múltiplos, classificações, filiais independentes, estados de loading, vazio e erro. Não foram criadas migrations nem alterados contratos do backend.
