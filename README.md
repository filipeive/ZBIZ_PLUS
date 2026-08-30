# ZBIZ+ — Plataforma Empresarial Modular e SaaS Multissetorial

> **ZBIZ+**: O Sistema de Gestão Inteligente que se Adapta ao Ritmo do Seu Negócio em Moçambique.

---

## 🌟 Visão Geral

O **ZBIZ+** é uma evolução tecnológica da experiência comprovada do ReproSys, transformado numa plataforma ERP/POS SaaS modular e escalável, concebida para atender os desafios operacionais, fiscais e económicos de pequenas, médias e grandes empresas em **Moçambique**.

### 🧩 Ecossistema Modular:
* **ZBIZ Core:** Núcleo ERP, multi-empresa, filiais, livro-razão imutável, gestão de salários e despesas.
* **ZBIZ POS:** Caixa de alta velocidade, leitor de código de barras, atalhos rápidos e modo *Offline-First*.
* **ZBIZ Pharmacy:** Vertical regulatório de farmácias com rastreabilidade de Lotes, Validades (alertas 30/60/90 dias), saída FEFO e conformidade com diretrizes da **ANARME**.
* **ZBIZ Retail:** Gestão de comércio, stock centralizado por filial e inventário dinâmico.
* **ZBIZ Repro / Services:** Ordens de trabalho, insumos vinculados e conversão de pedidos com adiantamento/sinal.
* **ZBIZ Finance & Payments:** Integração nativa com **M-Pesa** (C2B STK Push), **e-Mola** e reconciliação automática.
* **ZBIZ Analytics:** Inteligência de vendas, margem de lucro real e prevenção de rotura de stock.

---

## 📚 Documentação Técnica de Engenharia

Toda a arquitetura e governança do projeto estão documentadas na pasta [`docs/`](./docs):

* [`00_ENGINEERING_GUIDE.md`](./docs/00_ENGINEERING_GUIDE.md) — A Constituição Técnica do Projeto
* [`01_PRODUCT_VISION.md`](./docs/01_PRODUCT_VISION.md) — Visão de Produto, Personas e Módulos
* [`02_MARKET_RESEARCH_MOZAMBIQUE.md`](./docs/02_MARKET_RESEARCH_MOZAMBIQUE.md) — Pesquisa de Mercado e Concorrência
* [`03_ROADMAP.md`](./docs/03_ROADMAP.md) — Cronograma de Desenvolvimento Faseado
* [`04_ARCHITECTURE.md`](./docs/04_ARCHITECTURE.md) — Padrão Arquitetural Modular Monolith
* [`05_MULTI_TENANCY.md`](./docs/05_MULTI_TENANCY.md) — Isolamento de Dados por Tenant Scope
* [`06_DATABASE_SCHEMA.md`](./docs/06_DATABASE_SCHEMA.md) — Dicionário de Dados e Relacionamentos
* [`07_VERTICAL_MODULES.md`](./docs/07_VERTICAL_MODULES.md) — Módulos Verticais por Segmento
* [`08_POS_SPECIFICATION.md`](./docs/08_POS_SPECIFICATION.md) — Especificação do POS e Sincronização Offline
* [`09_PHARMACY_REGULATORY_GUIDE.md`](./docs/09_PHARMACY_REGULATORY_GUIDE.md) — Diretrizes ANARME, Lotes e FEFO
* [`10_BILLING_TAX_MOZAMBIQUE.md`](./docs/10_BILLING_TAX_MOZAMBIQUE.md) — NUIT, IVA (16%) e Faturação Fiscal
* [`11_PAYMENTS_MPESA_EMOLA.md`](./docs/11_PAYMENTS_MPESA_EMOLA.md) — Arquitetura de Pagamentos Móveis
* [`12_MIGRATION_FROM_REPROSYS.md`](./docs/12_MIGRATION_FROM_REPROSYS.md) — Guia de Importação de Dados do ReproSys
* [`adr/`](./docs/adr) — Architecture Decision Records (ADRs)

---

## 🚀 Instalação & Desenvolvimento

```bash
# 1. Clonar repositório
git clone git@github.com:filipeive/ZBIZ_PLUS.git
cd ZBIZ_PLUS

# 2. Instalar dependências PHP e Node.js
composer install
npm install

# 3. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 4. Executar migrações
php artisan migrate --seed

# 5. Executar testes
php artisan test

# 6. Iniciar servidor de desenvolvimento
php artisan serve
```

---

## 🛡️ Licença

Propriedade exclusiva — ZBIZ+ Moçambique.
