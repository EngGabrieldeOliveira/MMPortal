# API — Módulo de Clientes

## Base e autenticação

Todas as rotas abaixo usam o prefixo `/api/comercial`, exigem `Authorization: Bearer <token>` do Sanctum e retornam o envelope:

```json
{ "data": {}, "message": "", "errors": {} }
```

## Clientes

| Método | Rota | Permissão |
| --- | --- | --- |
| GET | `/clientes` | `clientes.visualizar` / perfis autorizados |
| POST | `/clientes` | `clientes.criar` / perfis autorizados |
| GET | `/clientes/{id}` | `clientes.visualizar` / perfis autorizados |
| PUT ou PATCH | `/clientes/{id}` | `clientes.editar` / perfis autorizados |
| PATCH | `/clientes/{id}/status` | `clientes.editar` / perfis autorizados |
| DELETE | `/clientes/{id}` | `clientes.excluir` / perfis autorizados |
| GET | `/clientes/{id}/contatos` | visualização |
| GET | `/clientes/{id}/enderecos` | visualização |
| GET | `/clientes/{id}/documentos` | visualização |
| GET | `/usuarios/opcoes` | usuários autorizados para responsável interno |

`DELETE` executa Soft Delete. Restauração e upload de documentos não possuem endpoint nesta Sprint.

### Listagem

Parâmetros: `page`, `limit` (1–100), `search`, `nome`, `fantasia`, `cnpj`, `cidade`, `contato`, `telefone`, `status`, `classificacao`, `sort` (`codigo`, `razao_social`, `nome_fantasia`, `status`, `created_at`) e `direction` (`asc`/`desc`).

### Payload principal

```json
{
  "tipo_pessoa": "juridica",
  "razao_social": "Estruturas Metálicas do Sul Ltda",
  "nome_fantasia": "Estruturas do Sul",
  "cpf_cnpj": "11222333000181",
  "inscricao_estadual": "123456789",
  "inscricao_municipal": "123456",
  "segmento": "Metalurgia",
  "condicao_pagamento_padrao": "30 dias",
  "limite_faturamento": 50000,
  "sem_limite_faturamento": false,
  "status": "ativo",
  "classificacoes": ["cliente", "fornecedor"],
  "endereco_cobranca": { "cep": "96835-120", "logradouro": "Rua das Indústrias", "cidade": "Santa Cruz do Sul", "estado": "RS" },
  "contatos": [{ "nome": "Ana Compras", "whatsapp": "51999999999", "email": "ana@empresa.test", "aprova_orcamentos": true, "is_principal": true }],
  "responsaveis": [{ "user_id": 1, "papel": "comercial" }]
}
```

Filial é criada pelo mesmo endpoint, com `matriz_id` apontando para a matriz. O CNPJ da filial permanece único e seus contatos e parâmetros financeiros são próprios.

`GET /usuarios/opcoes` retorna somente `id` e `name` de usuários dos perfis Administrador, Diretoria e Comercial. O responsável é persistido em `responsavel_id` e pode ser `null`.
