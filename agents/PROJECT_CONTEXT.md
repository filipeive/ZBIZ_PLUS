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
### 1. O que é o ZBIZ+? (Versão Atual 1.0.18+)
Uma plataforma SaaS modular e multi-tenant (ERP + POS) para Moçambique, atendendo diversos setores:
* **ZBIZ Core:** Núcleo multi-tenant com isolamento estrito (`BelongsToTenant`), filiais independentes, funcionários, contas e transações.
* **ZBIZ Owner Control Center (`/owner/tenants`):** Cockpit do Super Admin com métricas SaaS (MRR/ARR), emissão de chaves seriais (`ZBIZ-XXXX-...`), certificados oficiais PDF e acesso de suporte (Impersonate).
* **ZBIZ POS 2.0:** Frente de caixa rápida, suporte a leitor de código de barras, atalhos de teclado, descontos promocionais/manuais e operação offline-first.
* **ZBIZ Pharmacy:** Gestão farmacêutica com Lotes, Validades em 3 níveis (30/60/90 dias), saída FEFO, controlo de psicotrópicos e enquadramento ANARME.
* **ZBIZ Restaurant:** Gestão de Mesas & Sala (`/restaurant/tables`), estados de ocupação e comandas.
* **ZBIZ Universal Catalog:** Venda de produtos físicos e serviços universais para qualquer setor (farmácias, gráficas, oficinas, lojas).
* **ZBIZ Finance:** Livro-Razão imutável, Dívidas/Fiados por cliente, Despesas, Salários e pagamentos M-Pesa / e-Mola.

### 2. Principais Diretórios:
* `app/Models/` — Modelos Eloquent com `BelongsToTenant`
* `app/Services/` — Serviços de domínio (`FinancialLedgerService`, `DiscountService`)
* `app/Traits/` — Traits de tenancy e utilitários
* `database/migrations/` — Migrações estruturadas e seguras
* `docs/` — Documentação técnica viva do projeto
* `app/Services/` — Serviços de domínio (`FinancialLedgerService`, `LicenseService`, `DiscountService`)
* `app/Http/Controllers/` — Controladores finos segregados por domínio
* `resources/views/` — Blade templates integrados com TailwindCSS e Alpine.js
* `docs/` — Documentação técnica viva e apresentações comerciais
* `tests/` — Testes automatizados Pest / PHPUnit

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
