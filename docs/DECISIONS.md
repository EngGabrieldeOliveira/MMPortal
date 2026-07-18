# Decisões Arquiteturais

## Padrão visual interno reutilizável

Telas internas adotam cabeçalho, ações de formulário e navegação por seções reutilizáveis. As variantes semânticas de botão são centralizadas no design system escuro.

## Responsável interno único por cliente

O módulo de Clientes passou a usar `clientes.responsavel_id` como referência única ao usuário responsável. A migration preserva o primeiro vínculo existente em `cliente_responsaveis`; a tabela de histórico não é apagada. A escolha no frontend consome opções protegidas e não expõe dados sensíveis.

## Pessoa física e nome exibido

Pessoa física usa o mesmo atributo técnico de nome para compatibilidade, mas não recebe campos empresariais. Na listagem, PJ exibe nome fantasia (com fallback para razão social) e PF exibe o nome completo.

## ADR-001 — UUID somente para rastreabilidade transversal

Entidades de negócio mantêm IDs numéricos porque já existem relações, rotas e códigos sequenciais dependentes deles. Alterar chaves agora elevaria o risco sem ganho proporcional. UUID foi aplicado a auditoria e eventos, onde agrega correlação segura.

## ADR-002 — Soft Delete antes de exclusão definitiva

Registros comerciais e operacionais não são removidos fisicamente pelas rotas. Isso preserva rastreabilidade, documentos e referências históricas.

## ADR-003 — RBAC nativo sem pacote externo

Gates, middleware e Eloquent do Laravel atendem o estágio atual. Uma dependência externa só deve ser considerada com necessidade concreta de hierarquias complexas, multiempresa ou interface administrativa completa.

## ADR-004 — Auditoria por Observer

Observers Eloquent auditam mudanças mesmo quando elas ocorrem em Services, evitando duplicação nos controllers. Eventos administrativos continuam explícitos porque representam acontecimentos técnicos e de segurança.

## ADR-005 — Cadastro único com classificações múltiplas e filiais independentes

O MMPortal manterá um cadastro único por pessoa física ou jurídica, com relação N:N de classificações. As classificações aprovadas são `cliente`, `fornecedor` e `transportadora`; uma entidade pode acumular os três papéis, evitando duplicidade de CPF/CNPJ e permitindo reutilização por Comercial, Compras e Financeiro.

Filial é um cadastro `clientes` independente, vinculada à matriz por `matriz_id`. Portanto, possui CNPJ, contatos, endereço de cobrança, condição de pagamento e limite de faturamento próprios. Não será tratada como endereço nem herdará dados da matriz automaticamente.

Status descreve a condição operacional (`ativo`, `inativo`, `bloqueado`) e não substitui classificações. Parceiro e prospect não fazem parte das classificações aprovadas nesta versão da arquitetura.
