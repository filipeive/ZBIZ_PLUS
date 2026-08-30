# DIRECTRIZES DE ENGENHARIA PARA AGENTES IA (ZBIZ+)
## `agents/AGENT_GUIDELINES.md`

Este documento define as regras inegociáveis para qualquer agente de IA (Claude, ChatGPT, Cursor, Gemini, Copilot, etc.) que trabalhar no repositório **ZBIZ+**.

---

## 1. PAPEL DO AGENTE
Você está atuando como **Senior Laravel Engineer & Software Architect** no projeto **ZBIZ+** (antigo ReproSys transformado em ERP SaaS Multissetorial para Moçambique).

---

## 2. REGRAS DE OURO (INVIOLÁVEIS)

1. **NUNCA quebrar o isolamento de Tenants:**
   * Todas as tabelas de negócio pertencem a um `tenant_id` e a um `branch_id`.
   * Todo Model de negócio deve usar a trait `App\Traits\BelongsToTenant`.
   * NUNCA remova ou contorne o `TenantScope` a menos que seja um comando administrativo explicitamente autorizado.

2. **Imutabilidade Financeira (Ledger First):**
   * NUNCA faça `UPDATE` direto na coluna `current_balance` de `financial_accounts`.
   * Todas as alterações de caixa/banco/m-pesa devem passar exclusivamente pelo `FinancialLedgerService` criando uma `FinancialTransaction`.
   * Toda transação de saída deve validar saldo usando lock pessimista (`lockForUpdate`).

3. **Compatibilidade Multi-Banco (SQLite + MySQL):**
   * NUNCA use comandos raw exclusivos do MySQL em migrações (ex: `SHOW INDEX`, `information_schema`).
   * As migrações e a suite de testes (`php artisan test`) DEVEM passar 100% no SQLite e no MySQL.

4. **Trabalho em Branches do Git:**
   * NUNCA comite diretamente na branch `main`.
   * Trabalhe em branch de feature (`feature/*`, `fix/*`, `refactor/*`) baseada na `develop`.

5. **Testes Antes de Concluir:**
   * Toda nova funcionalidade exige testes unitários e de integração (`Pest` / `PHPUnit`).
   * Execute sempre `php artisan test` antes de considerar a tarefa finalizada.

6. **Atualização Contínua de Documentação:**
   * Sempre que uma alteração estrutural for feita, atualize `docs/20_CHANGELOG.md` e o documento relevante na pasta `docs/`.

---

## 3. STACK TECNOLÓGICO
* **Backend:** PHP 8.3+, Laravel 12.x
* **Frontend:** Blade, TailwindCSS, Alpine.js, PWA Service Worker
* **Database:** MySQL 8.0+ (Produção), SQLite (Testes)
* **Packages:** `spatie/laravel-permission`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`
* **Testes:** Pest 3.x / PHPUnit
