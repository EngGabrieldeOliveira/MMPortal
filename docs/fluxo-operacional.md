# Fluxo operacional

## Linha de negócio

`Solicitação → Orçamento (versionado) → Pedido → N ordens de serviço → Fabricação → Instalação → Conclusão`

Uma solicitação pode gerar vários orçamentos. Um orçamento aceito gera um único pedido com uma cópia imutável dos itens aprovados. Um pedido pode gerar quantas ordens de serviço forem necessárias, por exemplo, por ambiente, frente de instalação ou entrega parcial.

Cada OS nasce com as etapas sequenciais `fabricação` e `instalação`. A instalação não pode iniciar enquanto a fabricação da própria OS não estiver concluída. O histórico de qualquer mudança de estado é salvo em `historicos_status`.

## Estados

- Solicitação: `nova`, `em_analise`, `em_orcamento`, `orcamento_enviado`, `negociacao`, `postergada`, `encerrada`.
- Orçamento: `rascunho`, `enviado`, `aceito`, `recusado`, `postergado`.
- Pedido: `aberto`, `aguardando_pcp`, `em_planejamento`, `em_producao`, `aguardando_instalacao`, `concluido`, `cancelado`.
- Etapa da OS: `aguardando`, `planejada`, `em_andamento`, `concluida`, `bloqueada`.
