# Qualidade de código

## Análise estática

O backend usa Larastan `3.10.0` e PHPStan `2.2.5`, configurados em `backend/phpstan.neon` no nível 5. O comando oficial é `composer analyse`.

Não há baseline nem `ignoreErrors` configurados. A análise cobre `app` e `routes`; testes permanecem fora do escopo inicial.

## Fluxo de validação

1. `composer analyse`
2. `php artisan test`
3. `vendor/bin/pint --test`
4. `npm run lint`
5. `npm run build`

## Resultado atual

A causa raiz da execução silenciosa era a opção removida `checkMissingIterableValueType`, incompatível com PHPStan 2.x. A análise partiu de 50 erros e terminou com zero erros no nível 5; a última correção preservou o fallback de obra para pedidos sem Ordem de Serviço no `DashboardService`.

O comando oficial permanece `composer analyse`. Não há baseline nem `ignoreErrors` configurados.
