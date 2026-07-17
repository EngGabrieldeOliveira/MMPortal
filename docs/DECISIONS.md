# Decisões Arquiteturais

## ADR-001 — UUID somente para rastreabilidade transversal

Entidades de negócio mantêm IDs numéricos porque já existem relações, rotas e códigos sequenciais dependentes deles. Alterar chaves agora elevaria o risco sem ganho proporcional. UUID foi aplicado a auditoria e eventos, onde agrega correlação segura.

## ADR-002 — Soft Delete antes de exclusão definitiva

Registros comerciais e operacionais não são removidos fisicamente pelas rotas. Isso preserva rastreabilidade, documentos e referências históricas.

## ADR-003 — RBAC nativo sem pacote externo

Gates, middleware e Eloquent do Laravel atendem o estágio atual. Uma dependência externa só deve ser considerada com necessidade concreta de hierarquias complexas, multiempresa ou interface administrativa completa.

## ADR-004 — Auditoria por Observer

Observers Eloquent auditam mudanças mesmo quando elas ocorrem em Services, evitando duplicação nos controllers. Eventos administrativos continuam explícitos porque representam acontecimentos técnicos e de segurança.
