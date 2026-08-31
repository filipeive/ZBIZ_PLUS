# CHANGELOG: ZBIZ+
## `docs/20_CHANGELOG.md`

Todas as alterações notáveis no projeto ZBIZ+ serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

---

## [1.0.2] - 2026-08-31 - Migração Total para TailwindCSS & Temas Setoriais Dinâmicos
### Fixed
- **Inconsistência de Coluna `status` vs `is_active`:** Corrigidas todas as consultas SQL em `Category`, `ProductController`, `OrderController`, `CategoryController` e `SearchController`, adicionando o scope `scopeActive()` no model `Category`.
- **Rotas e Parâmetros de Finanças:** Correção de chamadas de rota em `layouts/app.blade.php`.

### Added
- **Helper de Temas Setoriais (`tenant_theme`):** Suporte nativo a paletas de cores automáticas por ramo de atividade:
  - 🟢 **Farmácia & Saúde (`pharmacy`):** Verde Esmeralda & Teal (`emerald-500` / `teal-600`)
  - 🔵 **Retalho & Comércio (`retail`):** Azul Céu & Indigo (`sky-500` / `indigo-600`)
  - 🟠 **Restaurante & Bar (`restaurant`):** Laranja & Rosa Quente (`orange-500` / `rose-600`)
  - 🟣 **Gráfica & Reprografia (`reprography`):** Violeta & Púrpura (`violet-500` / `purple-600`)
  - 🔷 **Prestação de Serviços (`services`):** Ciano & Teal (`teal-500` / `cyan-600`)
- **Migração Completa de Layout para TailwindCSS + Alpine.js:** Substituição do Bootstrap no `layouts/app.blade.php`, `dashboard/index.blade.php`, `categories/index.blade.php` e `products/index.blade.php` no mesmo design moderno, escuro e responsivo do ZBIZ POS 2.0.

## [1.0.1] - 2026-08-31 - Modernização de Views & Onboarding SaaS Interativo
### Added
- **Landing Page Oficial (`welcome.blade.php`):** Design moderno com TailwindCSS e Alpine.js, seletor de setor interativo (Retalho, Farmácia, Gráfica, Serviços), planos em Meticais (MZN) e chamada para trial de 30 dias.
- **SaaS Onboarding Wizard (`register.blade.php`):** Assistente passo a passo em 3 etapas para criação autónoma de empresas (Tenant), filial inicial, contas de caixa/M-Pesa e categorias padrão por setor.
- **Identidade Visual ZBIZ+ no Painel:** Atualização de `layouts/app.blade.php` com badge de filial ativa (`Loja Principal`), indicador de subscrição (`Plano Trial 30 Dias`) e atalho direto em destaque para o **ZBIZ POS 2.0**.
- **Testes de Navegação & Onboarding:** Suite `tests/Feature/Auth/TenantOnboardingTest.php` validada e testada no browser com 100% de aprovação.

## [1.0.0] - 2026-08-30 - Lançamento Oficial do ZBIZ+
### Added
- **M-Pesa C2B Integration:** Driver `MpesaDriver` com normalização de números moçambicanos (prefixos 84/85 e DDI 258) e chamadas STK Push.
- **Webhooks & Idempotência:** Endpoint `/api/webhooks/mpesa` com validação de assinatura, atualização atómica de pagamentos de subscrição e prevenção contra processamento duplicado de transações.
- **Gateway Manager:** `PaymentGatewayManager` extensível para múltiplos provedores móveis de Moçambique.
- Suite de testes de integração financeira e pagamentos móveis (`tests/Feature/Payments/`).

## [0.6.0] - 2026-08-30 - Fase 6: Verticais Especializadas (Farmácia ANARME & Gráfica/Insumos)
### Added
- **ZBIZ Pharmacy:** Model `ProductBatch` com controlo rigoroso de lotes e datas de validade.
- **Algoritmo FEFO (First Expired, First Out):** `PharmacyBatchService` para dispensação prioritária automática dos lotes com vencimento mais próximo e quarentena de lotes expirados.
- **Alertas Regulatórios ANARME:** Consultas parametrizadas de risco de validade (30, 60 e 90 dias).
- **Receitas Médicas:** Model `Prescription` para rastreio de prescrições e psicotrópicos.
- **ZBIZ Repro / Gráfica:** Model `ProductInsumo` e `InsumoManagerService` para vinculação de matérias-primas e dedução automática de stock na venda de serviços de cópia/impressão.
- Suite de testes automatizados para farmácia e gráfica (`tests/Feature/Verticals/`).

## [0.5.0] - 2026-08-30 - Fase 5: ZBIZ POS 2.0 (Frente de Caixa Rápida)
### Added
- Módulo `POSController` com endpoints ultrarrápidos para busca por código de barras, SKU e categorização dinâmica.
- Interface moderna Blade + TailwindCSS + Alpine.js (`resources/views/pos/index.blade.php`) com suporte a atalhos de teclado (F2 Buscar, F4 Cliente, F9 Checkout, ESC Cancelar).
- Suporte a multi-pagamento no POS: Dinheiro com cálculo de troco, M-Pesa, Cartão POS e Fiado/Crédito com criação imediata de dívida.
- Suporte a modo Offline-First com enfileiramento em cache local e sincronização automática via `/pos/sync-offline`.
- Layout de Impressão Térmica 80mm/58mm (`resources/views/pos/receipt.blade.php`) com NUIT do cliente/empresa, operador e data.
- Testes automatizados de frente de caixa e checkout (`tests/Feature/POS/POSCheckoutTest.php`).

## [0.4.0] - 2026-08-30 - Fase 4: Motor de Licenciamento SaaS & Planos
### Added
- Modelos `Plan`, `Subscription` e `SubscriptionPayment` para monetização SaaS.
- Seeder `PlanSeeder` com planos comerciais para o mercado moçambicano (Starter, Pro, Business, Pharmacy+, Enterprise).
- `SubscriptionService` para gestão de períodos de avaliação (Trial 30 dias), subscrições, upgrades e validação de limites de utilizadores e filiais.
- Middleware `CheckSubscriptionStatus` com fallback seguro para modo somente-leitura em caso de expiração da subscrição.
- Testes automatizados de licenciamento e limites (`tests/Feature/Licensing/SubscriptionAndPlansTest.php`).

## [0.3.0] - 2026-08-30 - Fase 3: Core ERP & Multi-Branch
### Added
- Entidade `Customer` com validação de NUIT moçambicano, controlo de limite de crédito (`credit_limit`) e recálculo automático de saldo devedor.
- Entidade `Supplier` para gestão de fornecedores e contas a pagar.
- Gestão de stock multi-filial com `ProductBranch` (quantidades isoladas por loja e armazém).
- Módulo de transferência de stock entre filiais (`StockTransfer` e `StockManagerService`).
- Turnos e Fecho de Caixa com contagem cega (`CashShift`).
- `FinancialLedgerService` aprimorado para auditoria atómica, sincronização de vendas/despesas e métricas consolidadas ou por filial.
- Suite completa de testes automatizados (`tests/Feature/Core/`).

## [0.2.0] - 2026-08-30 - Fase 2: Multi-Tenancy & Segurança
### Added
- Model `Tenant` para gestão de empresas clientes e subscrições.
- Model `Branch` para suporte nativo a múltiplas filiais por empresa.
- Trait `BelongsToTenant` com `TenantScope` automático.
- Middleware `IdentifyTenant` para resolução de tenant via subdomínio, sessão e headers de API.
- Testes de isolamento estrito entre tenants (`TenantIsolationTest`).

---

## [0.1.0] - 2026-08-30 - Fase 0 & 1: Discovery, Audit & Setup Base
### Added
- Inicialização do repositório oficial `ZBIZ_PLUS` (git@github.com:filipeive/ZBIZ_PLUS.git).
- Branches `main` e `develop` configuradas e sincronizadas remotamente.
- Constituição Técnica de Engenharia em `docs/00_ENGINEERING_GUIDE.md`.
- Documentação completa de visão, mercado moçambicano, roadmap, arquitetura, multi-tenancy, esquema de dados, POS, farmácia (ANARME), fiscalidade e pagamentos M-Pesa (`docs/01_...` a `docs/12_...`).
- ADRs 0001, 0002 e 0003 em `docs/adr/`.
- Diretrizes para Agentes de IA em `agents/AGENT_GUIDELINES.md` e `agents/PROJECT_CONTEXT.md`.
- Relatório completo de auditoria e descoberta arquivado em `docs/00_DISCOVERY_AUDIT_REPORT.md`.

### Fixed
- Correção de queries incompatíveis de `SHOW INDEX` e `information_schema` nas migrações legadas, viabilizando execução sem falhas em SQLite e MySQL.
- Consolidação da migração inicial do core business (`0001_01_01_000003_create_core_business_tables.php`).
