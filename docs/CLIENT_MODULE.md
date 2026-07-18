# Módulo de Clientes — Arquitetura aprovada

> A Sprint 06A definiu esta arquitetura. A Sprint 06B implementou o backend correspondente (schema, API, validações, Service, Resource, Policy, seeders e testes), sem alterações no frontend.

## 1. Princípios aprovados

- Um cadastro representa uma pessoa física ou jurídica e pode ter múltiplas classificações: **cliente**, **fornecedor** e **transportadora**.
- Filial não é endereço nem subregistro da matriz: é um **cadastro próprio**, com CNPJ, contatos e condição de pagamento próprios.
- Uma matriz pode possuir várias filiais por relacionamento explícito; uma filial possui no máximo uma matriz.
- Um cliente pode possuir várias obras. Obra é domínio operacional próprio e referencia o cliente; não é endereço nesta Sprint.
- Na primeira implementação, cada cadastro terá somente **endereço de cobrança**.
- Contatos, responsáveis internos e documentos são múltiplos. Documentos não possuem limite funcional.

O modelo atual em `clientes` continua como base transitória. A futura migração deve preservar `id`, `codigo` (`CLI-...`), Soft Delete e relações já existentes.

## 2. Modelo de domínio futuro

### 2.1 Cadastro principal: `clientes`

| Grupo | Campos propostos | Obrigatoriedade / observação |
| --- | --- | --- |
| Identidade | `id`, `codigo`, `tipo_pessoa` | Código legível e imutável; PF ou PJ. |
| Pessoa | `razao_social`, `nome_fantasia`, `cpf_cnpj` | Obrigatórios conforme decisão aprovada; documento normalizado para dígitos e único. Para PF, razão/nome fantasia representam os nomes legais/exibição do cadastro. |
| Fiscal | `inscricao_estadual`, `inscricao_municipal` | Campos obrigatórios no cadastro aprovado; regras de isenção serão detalhadas na Sprint de implementação. |
| Comercial | `segmento`, `status`, `observacoes_internas` | Segmento obrigatório; status sugeridos: `ativo`, `inativo`, `bloqueado`. |
| Financeiro | `condicao_pagamento_padrao`, `limite_faturamento`, `sem_limite_faturamento` | Condição em texto livre, limite opcional e indicador explícito para ausência de limite. Não modelar limite de crédito, contas bancárias ou SUFRAMA. |

## Ajustes finais de homologação

- Cada cliente possui no máximo um responsável interno, opcional, em `responsavel_id`.
- Pessoa física não utiliza razão social, nome fantasia, IE ou IM na interface; o nome completo permanece compatível com a estrutura existente.
- A tela de detalhes apresenta resumo, relacionamentos reais e estados vazios para dados ainda indisponíveis. Orçamentos, upload de documentos e timeline detalhada dependem de endpoints próprios.
| Relacionamento corporativo | `matriz_id` | Nulo para matriz/independente; aponta para outro `clientes.id` quando for filial. |
| Auditoria | `created_at`, `updated_at`, `deleted_at` | Mantém auditoria existente e Soft Delete. |

Cidade, estado e CEP pertencem ao endereço de cobrança, mas são obrigatórios no fluxo de cadastro por meio desse subregistro. Telefones, WhatsApp e e-mails pertencem a contatos; o fluxo exige pelo menos um contato cadastrado.

### 2.2 Classificações múltiplas

```text
clientes 1 ─── N cliente_classificacoes N ─── 1 classificacoes
```

| Tabela | Campos principais | Regra |
| --- | --- | --- |
| `classificacoes` | `id`, `chave`, `nome`, `ativo` | Catálogo com sementes: `cliente`, `fornecedor`, `transportadora`. |
| `cliente_classificacoes` | `cliente_id`, `classificacao_id`, timestamps | Único por par; permite todos os papéis no mesmo cadastro. |

O catálogo é preferido a enum para evoluir sem migration. Classificação não substitui o status operacional.

### 2.3 Matriz e filiais

```text
Cliente matriz 1 ─── N Cliente filial
                    └── matriz_id → clientes.id
```

Filial possui cadastro completo próprio: CNPJ, contatos, endereço de cobrança, condição de pagamento padrão, limite de faturamento, documentos e responsáveis. Dados não devem ser herdados automaticamente da matriz. A interface poderá oferecer “vincular à matriz existente”, mas nunca criar filial dentro de uma aba de endereços.

### 2.4 Endereço de cobrança

```text
clientes 1 ─── 1 cliente_endereco_cobranca
```

`cliente_enderecos`: `id`, `cliente_id`, `tipo='cobranca'`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`, `referencia`, timestamps e `deleted_at`.

Nesta fase, a regra é exatamente um endereço ativo de cobrança por cadastro. Entrega e obra ficam fora do escopo e somente poderão ser acrescentados em evolução posterior.

### 2.5 Contatos

```text
clientes 1 ─── N cliente_contatos
```

`cliente_contatos`: `id`, `cliente_id`, `nome`, `cargo`, `departamento`, `telefone`, `whatsapp`, `email`, `aprova_orcamentos`, `is_principal`, timestamps e `deleted_at`.

Permite múltiplos contatos. Um contato pode ser principal; vários podem aprovar orçamentos. Telefone, WhatsApp e e-mail são campos de contato e o modelo suporta múltiplos valores por cliente ao existir mais de um contato.

### 2.6 Responsáveis internos

```text
clientes 1 ─── N cliente_responsaveis N ─── 1 users
```

`cliente_responsaveis`: `cliente_id`, `user_id`, `papel`, timestamps. `papel` é opcional nesta primeira definição e pode indicar comercial, engenharia ou pós-venda no futuro. O par cliente/usuário deve ser único.

### 2.7 Documentos

```text
clientes 1 ─── N cliente_documentos
```

`cliente_documentos`: `id`, `cliente_id`, `tipo`, `nome_original`, `nome_exibicao`, `caminho`, `mime_type`, `tamanho`, `observacoes`, `enviado_por`, timestamps e `deleted_at`.

Anexos são ilimitados. Tipos iniciais: contrato, cartão CNPJ, procuração, ART, projeto, foto e outro. Armazenamento, download, visualização, auditoria e exclusão lógica devem reutilizar o padrão de `solicitacao_anexos`.

### 2.8 Relações consolidadas

```text
Cliente
├── matriz (N:1 Cliente, opcional) / filiais (1:N)
├── classificações (N:N)
├── endereço de cobrança (1:1 ativo)
├── contatos (1:N)
├── responsáveis internos (N:N User)
├── documentos ilimitados (1:N)
├── obras (1:N, domínio operacional futuro)
└── solicitações, orçamentos, pedidos e OS (integrações futuras)
```

## 3. Indicadores de cadastro e Dashboard

### Dados exibidos no detalhe

- **Cliente ativo desde:** data de início do status ativo. Requer futuro campo/evento `ativado_em`; não deve ser inferida de `created_at`.
- **Último pedido realizado:** data e código do pedido mais recente não removido, ou “sem pedidos”.

### Indicadores futuros do Dashboard

| Indicador | Definição planejada |
| --- | --- |
| Clientes ativos | Cadastros sem exclusão lógica com `status=ativo`. |
| Clientes inativos | Cadastros com `status=inativo`. |
| Novos clientes | Cadastros criados no período filtrado. |
| Clientes sem pedidos | Clientes sem pedido ativo/histórico conforme janela a definir. |
| Clientes sem contato | Sem contato principal ou sem interação depois que Timeline/Agenda tiver fonte de verdade. |

## 4. Contrato de API planejado

Todas as rotas futuras permanecem em `auth:sanctum` e usam o envelope:

```json
{ "data": {}, "message": "", "errors": {} }
```

| Método | Rota | Finalidade |
| --- | --- | --- |
| GET | `/api/comercial/clientes` | Lista paginada, filtros e ordenação. |
| GET | `/api/comercial/clientes/{id}` | Detalhe consolidado, incluindo matriz/filiais, indicadores e relações resumidas. |
| POST | `/api/comercial/clientes` | Criar cadastro e subregistros em transação. |
| PUT | `/api/comercial/clientes/{id}` | Atualizar cadastro e relações autorizadas. |
| DELETE | `/api/comercial/clientes/{id}` | Exclusão lógica. |
| GET | `/api/comercial/clientes/{id}/contatos` | Listar contatos. |
| GET | `/api/comercial/clientes/{id}/enderecos` | Retornar endereço de cobrança. |
| GET | `/api/comercial/clientes/{id}/documentos` | Listar anexos ilimitados. |

Filtros previstos: `search`, `nome`, `fantasia`, `cnpj`, `cidade`, `contato`, `telefone`, `status`, `tipo_pessoa`, `classificacao`, `responsavel_id`, `matriz_id`, `page` e `limit`. `search` pode combinar nome, fantasia, CNPJ, contato e telefone; filtros específicos são mantidos para precisão e índices futuros.

Não implementar ainda endpoints de escrita de contatos, endereço, responsáveis, documentos, filiais ou obras; eles serão detalhados na Sprint de implementação.

## 5. UX e wireframes aprovados

### Lista

```text
Clientes                                                     [+ Novo cadastro]
[ Buscar nome, fantasia, CNPJ, contato ou telefone ] [Status] [Classificação] [Mais filtros]
------------------------------------------------------------------------------------------------
Código       Razão social / Fantasia       Classificações    Cidade   Último pedido   Status  ...
CLI-000123   Metalúrgica Exemplo           Cliente · Fornecedor  RS   PED-000182      Ativo   ...
```

### Cadastro e edição

```text
Dados Gerais | Contatos | Cobrança | Responsáveis | Documentos | Matriz e Filiais
-----------------------------------------------------------------------------------
Dados Gerais (primeira aba)
Razão social | Nome fantasia | CPF/CNPJ | IE | IM | Segmento | Status
Condição de pagamento padrão | Limite de faturamento | Observações internas
```

O fluxo inicial exige os dados obrigatórios, um endereço de cobrança e ao menos um contato com os canais aplicáveis. A associação com matriz é uma seleção de outro cadastro; a criação de uma filial abre novo cadastro.

### Detalhes

```text
< Voltar  CLI-000123 · Metalúrgica Exemplo [Ativo]                  [Editar]
Cliente ativo desde: 14/07/2026       Último pedido: PED-000182
------------------------------------------------------------------------------------
Dados Gerais | Contatos | Cobrança | Responsáveis | Documentos | Matriz e Filiais | Timeline | Histórico
```

Timeline e Histórico seguem como planejamento. Timeline consolida eventos de negócio; Histórico expõe auditoria conforme RBAC.

## 6. Estrutura de pastas planejada

```text
backend/app/
├── Models/Cliente*.php
├── Http/Controllers/Api/Cliente/
│   ├── ClienteController.php
│   ├── ClienteContatoController.php
│   ├── ClienteEnderecoController.php
│   ├── ClienteDocumentoController.php
│   └── ClienteResponsavelController.php
├── Http/Requests/Cliente/
├── Services/Cliente/
└── Policies/ClientePolicy.php

frontend/src/features/clientes/
├── components/
├── hooks/
├── services/ClienteService.ts
├── types.ts
└── pages/
```

Services concentrarão transações, vínculo matriz/filial e garantia de principal. Controllers ficam com autorização, Form Requests e HTTP. Eloquent permanece a persistência; não criar Repository genérico.

## 7. Integrações futuras

| Módulo | Preparação |
| --- | --- |
| Solicitações | Seleção de cliente e contato. |
| Orçamentos | Destinatário e contatos com `aprova_orcamentos=true`. |
| Pedidos | Cliente/filial e último pedido no detalhe. |
| Ordens de Serviço | Cliente e vínculo futuro com obras. |
| Dashboard | Indicadores aprovados deste documento. |
| Financeiro | Condição de pagamento padrão e limite de faturamento. |
| Compras | Mesma entidade classificada como fornecedor ou transportadora. |

## 8. Riscos e melhorias antes da implementação

1. CPF/CNPJ deve ser normalizado antes da unicidade; a obrigatoriedade aprovada elimina prospect sem documento neste módulo.
2. IE/IM obrigatórias precisam de regra explícita para isentos antes de validação rígida.
3. A migração do cadastro atual deve copiar endereço e contato para entidades filhas sem romper `cliente_id` existente.
4. Vínculo matriz/filial não pode permitir ciclos; o futuro Service deve impedir matriz apontar para descendente.
5. Limite de faturamento não é limite de crédito e não deve bloquear pedidos sem regra financeira futura.
6. Definir a entidade Obra e a origem da Timeline/Agenda antes de calcular “sem pedidos” e “sem contato”.
7. Aplicar Policy/RBAC para dados fiscais e documentos, sem registrar binários ou informações sensíveis indevidas na auditoria.

## 9. Estado de implementação 06B

- Implementados: tabelas e modelos de classificações, contatos, endereço de cobrança, documentos, responsáveis internos, obras preparadas e eventos de timeline preparados.
- Implementados: REST de clientes, paginação, filtros, pesquisa, ordenação, CPF/CNPJ, Resource, Policy, permissões, auditoria por Observer, Soft Delete, factories e seeders mínimos.
- Não implementados por decisão de escopo: CRUD de obras, escrita específica dos sub-recursos, upload binário de documentos, restauração por endpoint e eventos funcionais de Timeline.

## 10. Estado de implementação 06C

- Telas implementadas: listagem paginada, cadastro, edição e detalhes nas rotas `/comercial/clientes`, `/comercial/clientes/novo`, `/comercial/clientes/:id` e `/comercial/clientes/:id/editar`.
- Integração: usa exclusivamente os endpoints REST reais de clientes e os sub-recursos de leitura disponíveis.
- Componentes reutilizáveis: toolbar administrativa, badges de classificação, paginação de servidor, skeleton, feedback de erro, abas de formulário e abas de detalhe usam a base visual existente do MMPortal.
- Limitações atuais: a API não oferece indicadores agregados de clientes, endpoint de usuários para selecionar responsáveis, restauração, upload de documentos, CRUD de obras ou eventos de Timeline. A interface os informa como indisponíveis e não cria dados simulados.
- Permissões: ações de criar, editar e excluir são condicionadas aos perfis autorizados no token; a API continua sendo a fonte final de autorização.
