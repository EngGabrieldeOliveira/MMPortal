# UI Guidelines

## Estrutura de página

Use `PageHeader` para breadcrumb, título, subtítulo, status, metadados e ações. Ações principais ficam à direita e seguem a ordem: ação neutra, ação de estado, ação destrutiva.

## Navegação interna

`SectionNavigation` recebe IDs reais da página e usa scroll suave e `IntersectionObserver` para indicar a seção ativa. Em desktop é lateral; em telas menores torna-se compacta.

## Cores semânticas

| Variante | Uso |
| --- | --- |
| `primary` / `warning` | salvar, criar e inativar |
| `success` | ativar e reativar |
| `secondary` | cancelar, voltar e editar |
| `danger` | excluir |
| `ghost` | ações discretas |

Não utilizar branco em ações de ativação ou inativação. Cards, tabelas e formulários devem manter fundo escuro, borda discreta e raio consistente.

## Formulários e listagens

Use grids consistentes, labels acima dos campos e `FormActions` para o rodapé. Listagens combinam `PageHeader`, filtros, tabela, paginação e estados de loading, erro ou vazio já existentes no design system.
