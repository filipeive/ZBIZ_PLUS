# CHANGELOG: ZBIZ+
## `docs/20_CHANGELOG.md`

Todas as alterações notáveis no projeto ZBIZ+ serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

---

## [Unreleased] - Fase 3: Core ERP & Multi-Branch
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
