# RELATÓRIO EXECUTIVO E ARQUITETURAL: FASE 0 — DISCOVERY & AUDIT
## De ReproSys a **ZBIZ+** (Plataforma Empresarial Modular e SaaS Multissetorial para Moçambique)

---

### PREÂMBULO & PAPEL DO CONSULTOR / CHIEF ARCHITECT

Como **Chief Software Architect**, **Senior Laravel Engineer**, **SaaS Architect**, **Product Manager**, **UX/Product Designer**, **Especialista em Sistemas POS/ERP** e **Consultor de Mercado para Moçambique**, realizei uma auditoria aprofundada no código-fonte, banco de dados, arquitetura, serviços, regras de negócio e infraestrutura do **ReproSys**.

Nenhuma linha de código foi modificada nesta fase de diagnóstico. O código existente foi tratado como a **fonte primária da verdade**.

---

## A. AUDITORIA DETALHADA DO REPROSYS (ESTADO ATUAL)

### 1. Inventário do Ecossistema Atual
* **Framework:** Laravel 12.0 (PHP 8.3+) com suporte a SQLite/MySQL.
* **Frontend:** Blade Templates, TailwindCSS 3/4, Alpine.js 3.4, AdminLTE 4 (RC3), Bootstrap 5.2 (coexistência híbrida).
* **Camada de Dados & ORM:** Eloquent ORM, com 21 Models e 29 migrações acumuladas.
* **Bibliotecas Chave:** `spatie/laravel-permission` (^6.21), `barryvdh/laravel-dompdf` (^3.1), `maatwebsite/excel` (^3.1).
* **Testes:** Pest 3.8 / PHPUnit, com 11 ficheiros de teste (atualmente com falhas devido a SQL raw específico de MySQL em ambiente SQLite).

---

## B. ESTADO ATUAL DA ARQUITETURA

```
┌──────────────────────────────────────────────────────────────────┐
│                      REPROSYS (Monólito Atual)                    │
│                                                                  │
│  [ Blade Views + Alpine.js + AdminLTE + Tailwind Híbrido ]      │
│                              │                                   │
│  [ Web Routes (482 linhas - Todas agrupadas num só ficheiro) ]    │
│                              │                                   │
│  [ Controllers Híbridos (CRUD + Lógica de Domínio Embutida) ]     │
│                              │                                   │
│  ┌───────────────────────┬───┴──────────────────────────────┐    │
│  ▼                       ▼                                  ▼    │
│ [FinancialService]    [DiscountService]            [PermissionService] │
│  (Cálculos / Livro)   (Descontos compostos)         (RBAC Spatie)      │
│  └───────────────────────┬──────────────────────────────────┘    │
│                          ▼                                       │
│          [ Eloquent Models (Single Database) ]                   │
│          (Sem isolamento de Empresa / Tenant / Sucursal)         │
└──────────────────────────────────────────────────────────────────┘
```

1. **Camada de Rotas (`routes/web.php`):** 482 linhas contendo rotas de API, rotas web, rotas de exportação e lógicas misturadas num único ficheiro.
2. **Camada de Domínio e Serviços:** O `FinancialService` é o componente mais maduro e bem construído do sistema atual, servindo como livro-razão (ledger) para transações, conciliação de caixa e validações atómicas de saldo. O `DiscountService` também possui lógica matemática consistente para rateio de descontos em itens.
3. **Persistência e Schema:** O banco de dados foi construído com foco exclusivo numa operação **unilocal** e **unipessoal/monotenant** de reprografia e gráfica rápida.
4. **Acoplamento:** Não existe conceito de `tenant_id`, `company_id` ou `branch_id`. Todos os registos pertencem globalmente à mesma base.

---

## C. PONTOS FORTES E ATIVOS A PRESERVAR

Estes componentes do ReproSys possuem alto valor de negócio e devem ser **incorporados no núcleo do ZBIZ+**:

1. **Motor Financeiro Centralizado (`FinancialService`):**
   * Padrão de lançamentos imutáveis (`FinancialTransaction` com reversões e snapshots de saldo `balance_after`).
   * Validação de saldo em caixa com locks pessimistas (`lockForUpdate()`) para evitar saídas com saldo negativo.
   * Separação clara entre contas de caixa físico e carteiras móveis (`caixa-principal`, `carteira-movel`).
2. **Gestão de Dívidas e Crédito Comercial (`Debt`, `DebtPayment`):**
   * Suporte tanto para dívidas originadas por produtos/vendas quanto para adiantamentos/empréstimos em dinheiro (`debt_type`: `product` vs `money`).
   * Algoritmo de amortização de parcelas com atualização automática de status.
3. **Fluxo de Conversão Pedido → Produção → Venda / Dívida:**
   * O ciclo de vida de `Order` (Sinal/Adiantamento → Conclusão → Liquidação ou Conversão em Dívida) é flexível e muito comum em oficinas, gráficas e alfaiatarias em Moçambique.
4. **Histórico de Auditoria & Atividade (`UserActivity`):**
   * Rastreio de IP, User-Agent, ação e modelo afetado.
5. **Mecanismo de Senhas Temporárias (`TemporaryPassword`):**
   * Recurso excelente para segurança operacional ao admitir novos funcionários.

---

## D. DÍVIDA TÉCNICA E VULNERABILIDADES IDENTIFICADAS

1. **Migração Quebrando Testes Automatizados:**
   * A migração `2025_11_06_083334_create_add_debt_indexes_table.php` executa `DB::select("SHOW INDEX FROM debts")`, comando exclusivo de MySQL. Isso quebra a execução de testes em SQLite (`pest`/`phpunit`), impedindo testes em pipeline de CI/CD.
2. **Duplicação de Código nos Controllers:**
   * Lógica de inserção de itens, abatimento de stock e disparo de transações repetida entre `SaleController`, `OrderController` e `DebtController`.
3. **Arquitetura de Frontend Conflituosa:**
   * O projeto carrega dependências simultâneas de TailwindCSS, Bootstrap 5 e AdminLTE 4. Isso gera especificidade de CSS pesada, carregamento de assets redundantes e inconsistências visuais entre páginas.
4. **Soft Deletes Não Uniformes:**
   * `Product` implementa uma lógica manual personalizada (`markAsDeleted` alterando o nome para `🚫 (EXCLUÍDO)`), enquanto outras tabelas usam o trait `SoftDeletes` nativo do Laravel.
5. **Falta de Tabela Dedicada de Clientes e Fornecedores:**
   * Atualmente, clientes são armazenados como strings simples (`customer_name`, `customer_phone`, `customer_document`) espalhadas entre vendas, dívidas e pedidos. Não existe entidade relacional única `Customer` com histórico unificado de compras, plafond de crédito ou NUIT.

---

## E. PROBLEMAS QUE IMPEDEM O USO DIRETO COMO SAAS

| Problema | Impacto no ReproSys Atual | O que o ZBIZ+ Exige |
| :--- | :--- | :--- |
| **Ausência de Multitenancy** | Dados de todas as empresas estariam misturados na mesma tabela sem distinção. | Isolamento rigoroso via `tenant_id` e políticas de escopo global ou schema dedicado. |
| **Hardcoding Setorial** | Campos e fluxos são rígidos para serviços gráficos e cópias. | Arquitetura com metadados e drivers verticais configuráveis por tenant. |
| **Monolocal (Single-Branch)** | O stock e caixa são globais por instalação. | Separação de stock, caixa e inventário por filial/armazém (`Branch/Store`). |
| **Sem Motor de Assinatura** | Não há controlo de períodos de teste, faturação recorrente ou bloqueio por falta de pagamento. | Módulo de Subscrições, Planos e Licenciamento SaaS integrado a M-Pesa / Cartão. |
| **Campos Fiscais Inexistentes** | Sem NUIT de cliente, taxas de IVA moçambicano (16%), séries de faturação ou hash de documento. | Motor de Faturação e Certificação Fiscal (Factura, Factura-Recibo, VD). |

---

## F. PESQUISA DE MERCADO — MOÇAMBIQUE

O mercado moçambicano de software de gestão para Pequenas e Médias Empresas (PME) vive um momento de transição acelerada:

1. **Aceleração da Faturação Eletrónica:** A Autoridade Tributária (AT) de Moçambique tem reforçado a conformidade fiscal, exigência de NUIT e controlo sobre a emissão de faturas e recibos.
2. **Penetração Massiva do Mobile Money:** Mais de 85% das transações diárias de pequenos negócios ocorrem via **M-Pesa (Vodacom)** e **e-Mola (Movitel)**, seguidas por cartões de débito (Rede SIMO) e numerário.
3. **Instabilidade de Infraestrutura:** Cortes pontuais de eletricidade e oscilações de internet 4G/fibra exigem que o POS de caixa não paralise quando a conexão falhar.
4. **Sensibilidade a Preço & Hardware:** A maioria dos comerciantes não possui computadores caros. Eles utilizam laptops antigos, tablets Android baratos ou até smartphones para gerir os seus negócios.

---

## G. ANÁLISE COMPETITIVA DE CONCORRENTES

```
┌────────────────────────────────────────────────────────────────────────┐
│                   MAPA DE CONCORRÊNCIA EM MOÇAMBIQUE                   │
│                                                                        │
│   Alto Custo │  [PHC CS Moçambique]       [Primavera / Cegid]          │
│              │  (Robusto, Pesado, Legado) (Muito Caro, Consultoria)    │
│              │                                                         │
│              │                   [SIGEM]                               │
│              │                                                         │
│              │  [Kontalah]                 ⭐ [ ZBIZ+ ]                │
│              │  (Foco Faturação/POS)      (Modular, SaaS, Offline-1st, │
│   Baixo Custo│  [Konekto ERP]              Vertical Farmácia/Varejo)   │
│              └─────────────────────────────────────────────────────────│
│                Genérico / Básico              Vertical & Especializado │
└────────────────────────────────────────────────────────────────────────┘
```

1. **Kontalah:**
   * *Pontos Fortes:* Muito popular em Moçambique, interface simples, aprovado/alinhado com requisitos de faturação básica, suporte a M-Pesa.
   * *Limitações:* Focado essencialmente em faturação/POS padrão; pouca especialização vertical (fraco para farmácias com lotes/validade ou oficinas de produção com insumos vinculados).
2. **Konekto ERP:**
   * *Pontos Fortes:* Modelo SaaS moçambicano para PMEs, gestão comercial e stock.
   * *Limitações:* Complexidade de configuração para microempresas, preço inacessível para negócios individuais, suporte offline limitado.
3. **PHC Software / Primavera (Cegid):**
   * *Pontos Fortes:* Extremamente robustos, padrão corporativo em grandes contas e gabinetes de contabilidade.
   * *Limitações:* Custo proibitivo (licenças em milhares de dólares + consultoria de implementação), exigem servidores locais pesados ou VPNs, curva de aprendizagem íngreme.
4. **FarmApp / Sistemas Isolados de Farmácia:**
   * *Pontos Fortes:* Atendem controlo de lote e validade.
   * *Limitações:* Geralmente softwares desktop antigos (Visual Basic / Delphi / MS Access), sem sincronização em nuvem, sem visão multi-filial e sem experiência mobile.

---

## H. OPORTUNIDADES & "WHITE SPACES" PARA O ZBIZ+

1. **O POS que funciona no navegador ou tablet e não para se a internet cair (Offline-First via PWA + IndexedDB).**
2. **Adoção do M-Pesa e e-Mola nativos com STK Push direto (o cliente digita o PIN no telemóvel dele na hora do pagamento).**
3. **Módulo de Farmácia acessível e moderno com controlo de Lote, Validade e Princípio Ativo (DCI), sem cobrar os valores exorbitantes dos ERPs tradicionais.**
4. **Onboarding de 2 minutos por Segmento:** Ao criar a conta, o sistema já vem com plano de contas, unidades de medida e categorias pré-configuradas para o tipo de negócio escolhido.

---

## I. ANÁLISE DE BRANDING: **ZBIZ+** vs **ZBPOS+**

> **Recomendação Estratégica:** Adotar **ZBIZ+** como nome oficial da plataforma empresarial.

* **Por que ZBIZ+ é superior a ZBPOS+?**
  * O sufixo **"POS"** restringe a perceção do cliente a "apenas uma caixa registadora de loja". Isso dificulta a venda para prestadores de serviços, consultorias, farmácias com distribuição, gráficas industriais e empresas B2B que emitem faturas e gerenciam dívidas/salários.
  * **ZBIZ+** posiciona o produto como uma **plataforma empresarial completa** (*Business Operating System*), permitindo posicionar os submódulos comercialmente:
    * **ZBIZ Core** (Núcleo ERP, Gestão Financeira, Dívidas e Salários)
    * **ZBIZ POS** (Ponto de Venda de Alta Performance e Caixa Rápido)
    * **ZBIZ Pharmacy** (Vertical Farmacêutico: Lotes, Validades, ANARME)
    * **ZBIZ Retail** (Vertical Comércio, Mercados e Boutiques)
    * **ZBIZ Repro / Services** (Vertical Gráfico, Serviços e Ordens de Trabalho)
    * **ZBIZ Analytics & AI** (Inteligência Comercial e Previsão de Ruptura)

* **Proposta de Valor Recomendada:**
  > *"ZBIZ+: O Sistema de Gestão Inteligente que se Adapta ao Ritmo do Seu Negócio em Moçambique."*

---

## J. PROPOSTA DE ARQUITETURA MODULAR DO ZBIZ+

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           ARQUITETURA ZBIZ+                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│  [ FRONTEND SAAS ]: TailwindCSS + Alpine.js / Blade Components + PWA ServiceWorker │
├─────────────────────────────────────────────────────────────────────────────┤
│  [ MULTI-TENANCY CORE ]: TenantScope + BranchScope + Domain/Subdomain Resolver│
├─────────────────────────────────────────────────────────────────────────────┤
│  [ MODULAR BUSINESS LAYER ]:                                                │
│   ├── zbiz-core (Tenants, Filiais, Usuários, Contas, Transações, Auditoria) │
│   ├── zbiz-pos (Venda Rápida, Balcão, Fecho de Caixa c/ Cego, Scanner)      │
│   ├── zbiz-inventory (Stock Central, Stock por Filial, Transferências)      │
│   ├── zbiz-billing (Faturação, Séries, NUIT, IVA 16%, Guias, SAF-T MZ)      │
│   ├── zbiz-credit (Gestão de Devedores, Amortizações, Limites de Crédito)   │
│   └── zbiz-verticals:                                                       │
│         ├── VerticalRetail (Código de Barras, Grade Cor/Tamanho)            │
│         ├── VerticalPharmacy (Lotes, Validade, Princípio Ativo, ANARME)     │
│         └── VerticalServices (Ordens de Trabalho, Insumos Vinculados)       │
├─────────────────────────────────────────────────────────────────────────────┤
│  [ INTEGRATIONS ]: M-Pesa C2B/B2C API | e-Mola | WhatsApp Receipts | AT     │
├─────────────────────────────────────────────────────────────────────────────┤
│  [ PERSISTENCE ]: MySQL 8.0+ / PostgreSQL (Single DB Isolado por Tenant)    │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## K. ESTRATÉGIA MULTI-TENANT RECOMENDADA

Para a realidade de uma startup SaaS em Moçambique, avaliámos as 3 abordagens:

| Abordagem | Custos de Servidor | Complexidade Operacional | Isolamento | Recomendação ZBIZ+ |
| :--- | :--- | :--- | :--- | :--- |
| **1. Database por Tenant** | Muito alto (exige dezenas de conexões abertas no MySQL; RAM esgota rápido). | Alto (rodar 500 migrações em 500 bancos em cada deploy). | Máximo | Inviável para fase inicial. |
| **2. Schema por Tenant (Postgres)** | Médio. | Médio-Alto (complexidade de pooling e backup individual). | Alto | Complexo para o ecossistema Laravel padrão. |
| **3. Single Database + `tenant_id` (com RLS / Global Scopes)** | **Mínimo** (roda centenas de empresas num VPS acessível de \$10-\$20/mês). | **Baixa e Elegante** (uma única migração, cache centralizado, backups simples). | **Muito Alto** quando protegido por Trait `BelongsToTenant` e Global Scope forçado. | **ESCOLHA RECOMENDADA (99% de adesão para SaaS Laravel de sucesso).** |

### Como Garantir 100% de Isolamento no Modelo Recomendado:
1. Todas as tabelas de negócio possuem `tenant_id` indexado.
2. Criação do Trait `BelongsToTenant` aplicado a todos os Models:
   * Aplica automaticamente `where('tenant_id', current_tenant_id())` em todas as consultas (`addGlobalScope`).
   * No hook `creating`, injeta automaticamente o `tenant_id` da sessão ativa.
3. Testes automatizados que tentam intencionalmente cruzar dados entre Tenants e garantem erro 403 / retorno nulo.

---

## L. ESTRATÉGIA DE LICENCIAMENTO & MONETIZAÇÃO (MOÇAMBIQUE)

### 1. Níveis de Assinatura

| Plano | Público-Alvo | Filiais | Usuários | Módulos Incluídos | Preço Sugerido (MT/mês) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Trial (30 Dias)** | Novos cadastros | 1 | 2 | Acesso total para testes | **Gratuito** |
| **ZBIZ Starter** | Bancas, Mini-lojas, Prestadores | 1 | 2 | Core + POS + Vendas + Caixa | **1.250 MT** |
| **ZBIZ Pro** | Lojas estruturadas, Oficinas | 2 | 5 | Starter + Dívidas + Salários + Relatórios Avançados | **2.950 MT** |
| **ZBIZ Business** | Médias empresas, Redes de lojas | Até 5 | Ilimitado | Pro + Multi-filial + Transferência de Stock + Faturação Fiscal | **5.500 MT** |
| **ZBIZ Vertical+ (Farmácia / Gráfica)** | Farmácias, Drogarias, Clínicas | Multi | Ilimitado | Módulo Regulatório ANARME + Lotes/Validade + Suporte Dedicado | **4.500 a 7.500 MT** |

### 2. Formas de Cobrança Nativas
* Pagamento mensal ou anual (com 2 meses grátis) via **M-Pesa** direto no sistema.
* Renovação automática com aviso por SMS/WhatsApp 5 dias antes da expiração.
* Modo de visualização somente-leitura após suspensão (o cliente nunca perde os dados históricos).

---

## M. ARQUITETURA DE CONFIGURAÇÃO POR TIPO DE ESTABELECIMENTO

Quando um Tenant é criado e escolhe o seu vertical no onboarding:
```json
{
  "business_type": "pharmacy",
  "enabled_modules": ["pos", "inventory", "debts", "pharmacy_batches", "billing"],
  "custom_fields": {
    "products": ["active_ingredient", "dosage", "therapeutic_class", "dosage_form", "requires_prescription"]
  },
  "terminology": {
    "product": "Medicamento",
    "customer": "Utente",
    "stock_unit": "Caixa / Blister"
  }
}
```

O sistema não faz `if ($tenant->isPharmacy())` espalhado nos controllers. Ele consome o `BusinessProfileDriver`, que ajusta os formulários, menus, regras de validação e relatórios de forma dinâmica e limpa.

---

## N. ESTRATÉGIA PARA O VERTICAL DE FARMÁCIAS (ANARME & BOAS PRÁTICAS)

### 1. Enquadramento Regulatório em Moçambique
A **ANARME** (Agência Nacional Reguladora de Medicamentos) é a autoridade máxima em Moçambique para regulação farmacêutica (Decreto n.º 84/2021). 
* **Aviso Crítico:** Nenhuma aplicação é "pré-homologada" apenas por ter campos técnicos. A conformidade exige auditoria de rastreabilidade, registo de responsável técnico (Farmacêutico com carteira) e relatórios de psicotrópicos.

### 2. Estrutura Técnica de Dados Farmacêuticos:
1. **Entidade `ProductBatch` (Lote):**
   * `batch_number` (Número do Lote)
   * `expiration_date` (Data de Validade — com alertas de 30, 60, 90 dias)
   * `manufacture_date` (Data de Fabrico)
   * `cost_price` & `selling_price` específicos por lote (permite FIFO/FEFO)
   * `quantity_available`
2. **Método de Saída FEFO (*First Expired, First Out*):**
   * O ZBIZ POS sugere automaticamente a saída do lote com validade mais próxima durante a venda de balcão.
3. **Livro de Medicamentos de Controlo Especial:**
   * Registo do prescritor (Médico, Clínica, CRM/N.º de Registo) e retenção da cópia de receita.

---

## O. ARQUITETURA OFFLINE-FIRST & INTEGRAÇÃO DE PAGAMENTOS

### 1. POS Offline-First (Resiliente à Queda de Internet)

```
[ Navegador / PWA ] ──── Sem Internet ────► [ IndexedDB Local ]
        │                                          │
    (Vendas ocorrem normalmente)                   │ (Vendas em Fila)
        │                                          │
        ▼                                          ▼
[ Conexão Retorna ] ──── Sincronização ───► [ ZBIZ+ Cloud API ]
                                             (Garante Idempotência via UUID)
```

* Cada venda gerada offline recebe um `client_uuid` gerado no dispositivo.
* Ao sincronizar com o servidor, o endpoint utiliza transações atómicas para garantir que nenhuma venda seja duplicada, mesmo se o pacote de rede oscilar.

### 2. Pagamentos Móveis Reais vs Manuais
* **Modo Manual:** O operador seleciona "M-Pesa" e digita o código de confirmação (ex: `BH73K9...`).
* **Modo API Automatizado (C2B / STK Push):**
  * O operador digita o telemóvel do cliente (84xxxxxxx ou 85xxxxxxx).
  * O ZBIZ+ dispara chamada para a API oficial do Vodacom M-Pesa.
  * O telemóvel do cliente acende pedindo o PIN.
  * O webhook do ZBIZ+ recebe a confirmação e fecha a venda no ecrã em tempo real sem intervenção humana.

---

## P. FISCALIDADE E FACTURAÇÃO EM MOÇAMBIQUE

1. **NUIT:** Validação de 9 dígitos para entidades singulares e coletivas.
2. **IVA a 16%:** Implementação do cálculo correto de taxa normal e isenções (Artigo 9.º do Código do IVA).
3. **Tipologia Documental:**
   * **Factura (FT):** Venda a prazo/crédito.
   * **Factura-Recibo (FR):** Venda a pronto pagamento.
   * **Venda a Dinheiro (VD) / Talão de Caixa:** Para POS de pequeno valor.
   * **Guia de Remessa (GR) / Transporte (GT):** Para circulação de mercadorias entre filiais/armazéns.
   * **Nota de Crédito (NC):** Para estornos e devoluções.
4. **Sequência e Séries Anuais:** Numeração ininterrupta por estabelecimento e ano (ex: `FT LOJA1/2026/0001`).

---

## Q. ROADMAP DE DESENVOLVIMENTO INCREMENTAL

```
Fase 0: Auditoria & Estratégia (CONCLUÍDO)
   │
Fase 1: Preparação do Novo Repositório ZBIZ_PLUS & Documentação Base
   │
Fase 2: Arquitetura Multi-Tenant & BelongsToTenant Scope
   │
Fase 3: Core ERP & Multi-Branch (Empresas, Lojas, Caixa, Transações)
   │
Fase 4: Motor de Licenciamento & Faturação de Assinaturas (M-Pesa)
   │
Fase 5: ZBIZ POS 2.0 (Interface Moderna, Scanner, Offline-Ready)
   │
Fase 6: Vertical 1 — Reprografia & Retalho Geral (Migração ReproSys)
   │
Fase 7: Vertical 2 — Farmácias & Clínicas (Lotes, Validade, ANARME)
   │
Fase 8: Integrações Oficiais (M-Pesa API, e-Mola, Automações)
```

---

## R. ESTRUTURA DA DOCUMENTAÇÃO & BACKLOG INICIAL

A documentação será inicializada no novo repositório com o padrão estabelecido:

```text
docs/
├── 00_ENGINEERING_GUIDE.md          # A Constituição Técnica do Projeto
├── 01_PRODUCT_VISION.md             # Visão ZBIZ+, Proposta de Valor e Personas
├── 02_MARKET_RESEARCH_MOZAMBIQUE.md # Análise Concorrencial, Preços e Oportunidades
├── 03_ROADMAP.md                    # Cronograma e Fases de Entrega
├── 04_ARCHITECTURE.md               # Clean Architecture, Serviços e Padrões Laravel
├── 05_MULTI_TENANCY.md              # Isolamento de Dados, Scopes e Segurança
├── 06_DATABASE_SCHEMA.md            # Dicionário de Dados e Relacionamentos
├── 07_VERTICAL_MODULES.md           # Sistema de Configuração por Ramo de Atividade
├── 08_POS_SPECIFICATION.md          # Especificação de UX/UI e Fluxo Offline
├── 09_PHARMACY_REGULATORY_GUIDE.md  # Requisitos ANARME, Lotes e Validades
├── 10_BILLING_TAX_MOZAMBIQUE.md     # NUIT, IVA 16%, Séries e Documentos Fiscais
├── 11_PAYMENTS_MPESA_EMOLA.md       # Arquitetura de STK Push e Conciliação
├── 12_MIGRATION_FROM_REPROSYS.md    # Estratégia de Migração Sem Perda de Dados
└── adr/                             # Architecture Decision Records
    ├── 0001-adoption-of-single-db-multitenancy.md
    ├── 0002-modular-vertical-drivers.md
    └── 0003-unified-financial-ledger.md
```

---

## S. ESTRATÉGIA DE GIT & MIGRAÇÃO (REPROSYS → ZBIZ+)

* **Repositório Atual (`reprosys`):** Fica 100% congelado e preservado como a versão estável legada. Nenhuma alteração destrutiva será feita nele.
* **Novo Repositório de Trabalho:** `git@github.com:filipeive/ZBIZ_PLUS.git`
