# RELATÓRIO DO ESTADO ATUAL DO SISTEMA: ZBIZ+ (v1.0.18)
## `docs/00_SYSTEM_STATUS_AND_ARCHITECTURE_2026.md`

---

## 1. VISÃO GERAL EXECUTIVA

O **ZBIZ+** é uma plataforma empresarial completa de **Gestão Comercial (ERP), Ponto de Venda (POS) e Controlo Financeiro Multi-Tenant**, arquitetada e desenvolvida especificamente para resolver os desafios do comércio, saúde e serviços em **Moçambique**.

* **Versão de Produção Atual:** `1.0.18`
* **Arquitetura:** Monólito Modular de Alta Coesão (Modular Monolith) com isolamento rigoroso por Tenant e Branch.
* **Stack Tecnológico:**
  * **Backend:** PHP 8.3+, Laravel 12.20.0
  * **Frontend:** Blade Templates, TailwindCSS, Alpine.js 3.x, FontAwesome 6
  * **Banco de Dados:** MySQL 8.0+ (Produção), SQLite em Memória (Ambiente de Testes Automatizados)
  * **Segurança:** RBAC via `spatie/laravel-permission`, Autenticação Laravel Breeze, Criptografia SHA-256 HMAC para Licenças Offline
  * **Suite de Testes:** Pest 3.x / PHPUnit (100% de testes de isolamento de dados aprovados)

---

## 2. PILARES ARQUITETURAIS IMPLEMENTADOS

### 2.1. Multi-Tenancy com Isolamento de Dados Inviolável
* **Trait `BelongsToTenant`:** Todas as entidades de dados do negócio (`Product`, `Sale`, `StockMovement`, `Expense`, `FinancialTransaction`, `Debt`, `Customer`, `RestaurantTable`, etc.) possuem `tenant_id` e aplicam automaticamente o `TenantScope`.
* **Multi-Filiais (`Branch`):** Suporte nativo a matrizes e filiais. Os operadores têm stock e caixas isolados por loja, enquanto a gestão executiva possui visão consolidada.
* **Middleware de Proteção:** `BelongsToTenant`, `CheckSubscriptionStatus`, `EnsurePlanFeature`.

### 2.2. Motor Financeiro Imutável (Ledger-First Financial Architecture)
* **Sem `UPDATE` Cego de Saldo:** O saldo das contas financeiras (`financial_accounts`) é calculado através de registos auditáveis de débito e crédito (`FinancialTransaction`).
* **Meios de Pagamento Nativos:** Numerário (Cash), M-Pesa, e-Mola, Cartão de Débito/POS, Transferência Bancária (Conta Móvel, Izi, Ponto24).
* **Gestão de Dívidas & Fiado:** Extrato detalhado por cliente (`/debts/{id}`), registo de amortizações parciais, controlo de limites de crédito e histórico de cobrança.
* **Despesas & Gestão Orçamental:** Classificação por categorias de despesa com geração de Recibos de Renda e folha de salários.

### 2.3. Ponto de Venda Inteligente (ZBIZ POS 2.0)
* **Venda Ultra-Rápida:** Pesquisa preditiva por nome, código de barras ou princípio ativo DCI.
* **Mecanismo Dual de Descontos:**
  * *Desconto Promocional Automático:* Aplicado automaticamente a artigos marcados em promoção dentro do prazo de vigência.
  * *Desconto Manual Negociado:* Inserção direta de percentagem (%) ou valor fixo (MT) autorizada pelo gestor.
* **Impressão Térmica:** Formatação para talões de 80mm e 58mm compatíveis com impressoras ESC/POS.

### 2.4. Módulos Verticais Especializados
1. **Farmácia & Saúde (`pharmacy`):**
   * Gestão de Lotes e Prazos de Validade;
   * Saída automática por FEFO (*First Expired, First Out*);
   * Radar de Validades com alertas a 90, 60 e 30 dias;
   * Controlo de Medicamentos Psicotrópicos com registo de prescrição médica;
   * Venda integrada de Serviços Farmacêuticos (injeções, medição de pressão arterial, testes rápidos).
2. **Restaurante & Bar (`restaurant`):**
   * Mapa de Mesas por filial (`/restaurant/tables`);
   * Gestão de ocupação (Livre, Ocupada, Reservada, Limpeza);
   * Integração com pedidos e comandas de mesa.
3. **Retalho & Supermercado (`retail`):**
   * Leitura ótica contínua, gestão de prateleira e alertas de stock mínimo.
4. **Gráfica & Reprografia (`reprography` / `services`):**
   * Encomendas de produção, consumo de papel/insumos vinculados e controle de sinais.

### 2.5. SaaS Owner Control Center (Painel do Dono)
* **Métricas SaaS em Tempo Real:** MRR (Receita Recorrente Mensal), ARR Projetado, total de clientes ativos, instalações offline e licenças prestes a expirar.
* **Chaves Seriais de Software:** Formato legível `ZBIZ-XXXX-XXXX-XXXX-XXXX` com botão de cópia instantânea na tabela de tenants.
* **Certificado Oficial de Licença:** Página pronta para impressão ou PDF com selo oficial, chave serial e assinatura HMAC SHA-256.
* **Onboarding Rápido:** Modal interativo de criação de tenant, filial matriz, administrador e emissão automática de licença num único clique.
* **Modo Suporte Técnico (Impersonate):** Acesso imediato ao ambiente do cliente com barra de status superior âmbar e botão de retorno seguro.

---

## 3. INVENTÁRIO DAS ROTAS E MÓDULOS PRINCIPAIS

| Módulo | Prefixo / Rota | Funcionalidades Chave |
| :--- | :--- | :--- |
| **Owner Console** | `/owner/tenants` | KPIs MRR/ARR, Gestão de Clientes, Chaves Seriais, Certificados, Impersonate |
| **Dashboard** | `/dashboard` | Resumo de vendas diárias, faturamento mensal, comparativos, gráficos de tendência |
| **POS 2.0** | `/pos` | Frente de caixa rápida, suporte a código de barras, atalhos, descontos |
| **Vendas & Manual** | `/sales`, `/sales/manual-create` | Histórico de transações, faturação manual detalhada, reimpressão |
| **Artigos & Serviços** | `/products` | Gestão de produtos físicos, catálogo de serviços universais, lotes e validades |
| **Stock & Armazém** | `/stock-movements` | Entradas de fornecedor, transferências entre lojas, quebras e acertos |
| **Fiados & Créditos** | `/debts` | Gestão de dívidas, extrato por cliente, liquidação de amortizações |
| **Livro-Razão & Caixa** | `/finances` | Saldo em caixa, entradas/saídas por conta (Numerário, M-Pesa, Banco) |
| **Despesas** | `/expenses` | Gastos operacionais, categorias de despesas, recibos de renda |
| **Mesas de Restaurante** | `/restaurant/tables` | Mapa de mesas, estados de ocupação por filial (Vertical Restaurante) |
| **Relatórios Executivos** | `/reports` | DRE, vendas por produto, vendas especializadas, curva ABC, validade |
| **Ativação Offline** | `/license/activate` | Desbloqueio e validação de chaves seriais em ambientes sem internet |

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: fdev-ms (FDS Multiservices)**  
*Engenharia de Software & Suporte Técnico:* `fdev-ms@fdevms:~/Filipe/reprosys$`  
*Contacto & Assistência:* (+258) 84 999 1122 · Quelimane / Moçambique  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
