# CONTEXTO RÁPIDO DO PROJETO ZBIZ+ PARA AGENTES
## `agents/PROJECT_CONTEXT.md`

### 1. O que é o ZBIZ+?
Uma plataforma SaaS modular (ERP + POS) para Moçambique, atendendo diversos setores:
* **ZBIZ Core:** Núcleo multi-tenant, filiais, funcionários, contas e transações.
* **ZBIZ POS:** Frente de caixa rápida, suporte a código de barras e offline-first.
* **ZBIZ Pharmacy:** Gestão farmacêutica com Lotes, Validades (30/60/90 dias), saída FEFO e regulatório ANARME.
* **ZBIZ Retail:** Comércio, lojas e distribuição.
* **ZBIZ Repro / Services:** Gráficas, oficinas e prestadores de serviços com insumos vinculados.
* **ZBIZ Finance:** Caixa, Dívidas a receber, Salários, Despesas e pagamentos M-Pesa / e-Mola.

### 2. Principais Diretórios:
* `app/Models/` — Modelos Eloquent com `BelongsToTenant`
* `app/Services/` — Serviços de domínio (`FinancialLedgerService`, `DiscountService`)
* `app/Traits/` — Traits de tenancy e utilitários
* `database/migrations/` — Migrações estruturadas e seguras
* `docs/` — Documentação técnica viva do projeto
* `tests/` — Testes automatizados Pest / PHPUnit
