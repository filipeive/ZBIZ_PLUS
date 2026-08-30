# ADR 0003: Livro-Razão Financeiro Centralizado e Imutável

## Contexto
Distorções financeiras em ERPs ocorrem quando múltiplos controllers alteram saldos diretamente.

## Decisão
Todo o fluxo financeiro do ZBIZ+ é gerido exclusivamente pelo `FinancialLedgerService` através da tabela `financial_transactions`. Nenhuma entidade altera saldo por update direto.

## Consequências
* 100% de rastreabilidade e auditoria financeira.
* Conciliação exata de caixa e carteiras móveis (M-Pesa/e-Mola).
