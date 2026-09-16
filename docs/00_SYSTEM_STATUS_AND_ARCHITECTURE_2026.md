# RELATÓRIO DO ESTADO ATUAL DO SISTEMA: ZBIZ+ (v1.0.20)
## `docs/00_SYSTEM_STATUS_AND_ARCHITECTURE_2026.md`

---

## 1. VISÃO GERAL EXECUTIVA

O **ZBIZ+** é uma plataforma empresarial completa de **Gestão Comercial (ERP), Ponto de Venda (POS) e Controlo Financeiro Multi-Tenant**, arquitetada e desenvolvida especificamente para resolver os desafios do comércio, saúde e serviços em **Moçambique**.

* **Versão de Produção Atual:** `1.0.21`
* **Arquitetura:** Monólito Modular de Alta Coesão (Modular Monolith) com isolamento rigoroso por Tenant e Branch.
* **Stack Tecnológico:**
  * **Backend:** PHP 8.3+, Laravel 12.20.0
  * **Frontend:** Blade Templates, TailwindCSS, Alpine.js 3.x, FontAwesome 6
  * **Banco de Dados:** MySQL 8.0+ (Produção), MariaDB 11.x (Local), SQLite em Memória (Testes Automatizados)
  * **Sessões & Cache:** Drivers em `file` para alta resiliência e velocidade em balcões monomáquina, com tela de diagnóstico de auto-recuperação (`errors.database`).
  * **Segurança & Autenticação:** Autenticação flexível (Email, Nome de Usuário, Telemóvel e Código de Funcionário), Recuperação de senha por E-mail ou SMS OTP, RBAC via `spatie/laravel-permission`, Soft Deletes para preservação de licenças/certificados, Criptografia SHA-256 HMAC para Licenças Offline
  * **Suite de Testes:** Pest 3.x / PHPUnit (Testes de isolamento de dados, licensing, sync e fallback 100% aprovados)

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

### 2.6. Auditoria de Caixa e Turnos (Fecho Cego Anti-Fraude)
* **Conceito Fecho Cego:** O operador encerra o turno sem conhecer o saldo esperado pelo sistema, eliminando desvios e sobras artificiais.
* **Calculadora MZN de Cédulas e Moedas:** Contagem física guiada das notas e moedas de Meticais moçambicanos.
* **Apuramento Automático:** Cálculo imediato de faltas (quebras) e excessos (sobras) de caixa para auditoria gerencial.
* **Comprovativo Fecho Z:** Impressão térmica com resumo financeiro por meio de pagamento e assinaturas.
* **Relatório de Fecho A4:** Versão completa para arquivo e auditoria, disponível em `/cash-shifts/{shift}/receipt-a4`.
* **Regra de operação diária:** Operadores não administradores só podem finalizar vendas depois de abrir um turno na data atual; administradores podem operar sem turno.
* **Regularização de turnos antigos:** Deve ser feita por um administrador/supervisor com contagem física e justificação, sem encerramento automático presumido.

### 2.7. Fiscalidade Moçambique (CIVA) e Modelo A da AT
* **Suporte à Lei do IVA:** Taxa normal de 16% e regime de isenção nos termos do Artigo 9º do CIVA (farmácias/medicamentos e bens de saúde).
* **Alternância Rápida no POS:** Modo IVA Incluso, Adicionar IVA (+16%) ou Isento de IVA (Art. 9º).
* **Mapa Fiscal Modelo A:** Demonstrativo oficial em conformidade com a Autoridade Tributária com exportação em PDF de alta qualidade.

### 2.8. Gestão de Clientes, Fornecedores e Contas Correntes
* **Clientes:** Cadastro rápido no balcão do POS, limite de crédito e extrato de dívidas.
* **Fornecedores:** Vínculo direto de distribuidores farmacêuticos nos produtos e controle de status ativo/inativo.

### 2.9. Backups & Segurança sob Demanda
* **Dump SQL Seguro:** Geração direta de cópias de segurança da base de dados pelas Definições do Sistema, com download e rotação de arquivos.
* **Branding Dinâmico:** Gestão de logotipo com fundo limpo neutro e aplicação automática em recibos térmicos (80mm/58mm) e relatórios oficiais.

### Nota de evolução financeira

O card de saldo exibido no Dashboard e o card equivalente em Finanças usam atualmente a mesma métrica (`currentCapital`), calculada pela soma dos saldos das contas operacionais ativas. Esta duplicação está documentada para futura racionalização: o saldo deve permanecer em Finanças como **Liquidez Atual**, enquanto o Dashboard deverá destacar **Fluxo Líquido do Mês** ou outro indicador operacional não redundante.

A regularização administrativa de turnos antigos e a validação da resolução de contas por tenant/filial estão documentadas em `docs/14_FUTURE_FINANCIAL_RECONCILIATION.md`.

---

## 3. INVENTÁRIO DAS ROTAS E MÓDULOS PRINCIPAIS

| Módulo | Prefixo / Rota | Funcionalidades Chave |
| :--- | :--- | :--- |
| **Owner Console** | `/owner/tenants` | KPIs MRR/ARR, Gestão de Clientes, Chaves Seriais, Certificados, Impersonate |
| **Dashboard** | `/dashboard` | Resumo de vendas diárias, faturamento mensal, comparativos, gráficos de tendência |
| **POS 2.0** | `/pos` | Frente de caixa rápida, suporte a código de barras, atalhos, descontos, clientes e IVA |
| **Caixas & Turnos** | `/cash-shifts` | Abertura com fundo de maneio, fecho cego com calculadora de MZN, Fecho Z térmico e Relatório A4 |
| **Clientes** | `/customers` | Fichas de clientes, histórico de compras, limite de crédito e saldos devedores |
| **Fornecedores** | `/suppliers` | Distribuidores, laboratórios, prazos comerciais e vínculo a produtos |
| **Mapa de IVA** | `/reports/tax-iva` | Apuramento Modelo A (AT Moçambique), isenção Art. 9º CIVA, exportação PDF oficial |
| **Vendas & Manual** | `/sales`, `/sales/manual-create` | Histórico de transações, faturação manual detalhada, reimpressão |
| **Artigos & Serviços** | `/products` | Gestão de produtos físicos, fornecedores vinculados, catálogo de serviços, lotes |
| **Stock & Armazém** | `/stock-movements` | Entradas de fornecedor, transferências entre lojas, quebras e acertos |
| **Fiados & Créditos** | `/debts` | Gestão de dívidas, extrato por cliente, liquidação e talão térmico de amortização |
| **Livro-Razão & Caixa** | `/finances` | Saldo em caixa, entradas/saídas por conta (Numerário, M-Pesa, Banco) |
| **Despesas** | `/expenses` | Gastos operacionais, categorias de despesas, recibos de renda |
| **Mesas de Restaurante** | `/restaurant/tables` | Mapa de mesas, estados de ocupação por filial (Vertical Restaurante) |
| **Relatórios Executivos** | `/reports` | DRE, vendas por produto, mapa de IVA, curva ABC, validade |
| **Backups & BD** | `/settings` (Aba 6) | Geração de dump SQL, download de cópias de segurança e exclusão |
| **Ativação Offline** | `/license/activate` | Desbloqueio e validação de chaves seriais em ambientes sem internet |

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.20*  
────────────────────────────────────────────────────────────────────────────  

