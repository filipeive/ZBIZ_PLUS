# ZBPOS+ — Arquitetura SaaS, Central de Controle & Isolamento de Filiais

## 1. Visão Geral da Arquitetura

O **ZBPOS+** opera sob uma arquitetura híbrida de **SaaS Multi-Tenant** e **Multi-Branch (Multi-Estabelecimentos)** projetada para suportar empresas comerciais, farmácias reguladas (ANARME) e redes varejistas com isolamento estrito de dados, faturamento e estoque.

---

## 2. Hierarquia Estrutural da Plataforma

```
ZBPOS+ Platform (Nossa Empresa SaaS)
   │
   ├── Control Plane (Central de Controle ZBPOS+)
   │     ├── Gestão de Tenants (Empresas Clientes)
   │     ├── Planos Comerciais (Catálogo de Preços & Limites)
   │     ├── Subscrições & Ciclo de Vida (Trial, Active, Suspended)
   │     ├── Licenças & Chaves de Ativação (Online / POS Kiosk)
   │     └── Entitlements / Módulos (Feature Flags por Tenant)
   │
   └── Tenant Application (Instância / Espaço do Cliente)
         │
         └── Empresa Cliente (Ex: Farmácia Muzinga, Lda)
               │
               ├── Filial A: Matriz Maputo (Av. 24 de Julho)
               │     ├── Estoque Local (ProductBranch)
               │     ├── Caixa Físico POS
               │     ├── Operadores / Vendedores
               │     └── Vendas & Despesas Locais
               │
               └── Filial B: Filial Matola (Av. da Liberdade)
                     ├── Estoque Local (ProductBranch)
                     ├── Caixa Físico POS
                     ├── Operadores / Vendedores
                     └── Vendas & Despesas Locais
```

---

## 3. Conceitos Fundamentais SaaS

| Conceito | Definição Arquitetural | Exemplo no ZBPOS+ |
| :--- | :--- | :--- |
| **Plan (Plano)** | O pacote comercial definido no catálogo, com preços (mensal/anual) e quotas máximas. | *Starter, Professional, Enterprise, Pharmacy Plus* |
| **Subscription (Subscrição)** | A associação temporal de um Tenant a um Plano, controlando períodos de vigência e pagamento. | *Farmácia Muzinga no Plano Professional (Status: Active, Expira: 01/09/2027)* |
| **License (Licença)** | A autorização técnica criptográfica ou chave de ativação para instâncias ou pontos de venda locais. | *Chave `ZBPOS-2026-MUZ-MAT01` com assinatura digital RSA* |
| **Entitlement (Direito/Módulo)** | A permissão efetiva que habilita ou desabilita capacidades de software para o Tenant. | `pos`, `inventory`, `multi_branch`, `pharmacy_anarme`, `ai_insights`, `financial_ledger` |
| **Feature Flag** | A verificação centralizada em runtime que valida se a empresa possui o entitlement ativo. | `$tenant->hasFeature('pharmacy_anarme')` |
| **Branch Context** | O escopo operacional ativo onde vendas, despesas, caixa e movimentações de estoque ocorrem. | Contexto selecionado no Topbar Switcher (`current_branch_id()`) |

---

## 4. Isolamento Operacional por Filial (Branch Isolation)

### 4.1 Resolução de Contexto no Middleware (`IdentifyTenant.php`)
1. **Prioridade 1 — Sessão Ativa (`session('current_branch_id')`)**: Permite que Administradores e Gerentes alternem rapidamente entre filiais através do menu superior sem perder o contexto do Tenant.
2. **Prioridade 2 — Filial Atribuída ao Utilizador (`auth()->user()->branch_id`)**: Padrão atribuído para operadores de caixa e funcionários restritos a uma loja física.
3. **Prioridade 3 — Filial Principal (`tenant->mainBranch`)**: Fallback automático para a matriz da empresa.

### 4.2 Modelo de Dados Isolados vs Compartilhados

| Entidade | Nível de Associação | Comportamento Operacional |
| :--- | :---: | :--- |
| **Catálogo de Produtos** | `tenant_id` | Catálogo global de artigos unificado para a empresa. |
| **Estoque Físico (`product_branches`)** | `tenant_id` + `branch_id` | Quantidade em estoque, estoque mínimo e prateleira isolados por filial. |
| **Lotes & Validades (`product_batches`)** | `tenant_id` + `branch_id` | Lotes ANARME específicos alocados em cada armazém/loja. |
| **Vendas & Faturas (`sales`, `sale_items`)** | `tenant_id` + `branch_id` | Cada venda é faturada e emitida no contexto da filial ativa. |
| **Contas de Caixa (`financial_accounts`)** | `tenant_id` + `branch_id` | Caixas físicos e gavetas de dinheiro separadas por loja. |
| **Despesas (`expenses`)** | `tenant_id` + `branch_id` | Custos operacionais e compras alocadas à filial emitente. |
| **Transferências (`stock_transfers`)** | `tenant_id` + `source/destination` | Rastreabilidade entre a filial de origem e de destino. |

---

## 5. Especificação da Central de Controle ZBPOS+ (Control Plane)

### 5.1 Propósito
A **Central de Controle** é a interface restrita da equipe gestora do ZBPOS+ (Super Admins) para gerir clientes sem acessar os dados confidenciais de negócio de cada tenant.

### 5.2 Módulos da Central de Controle:
1. **Painel de Tenants (Clientes)**:
   - Listagem de empresas cadastradas, status (`trial`, `active`, `suspended`, `cancelled`), NUIT, e-mail e responsável.
   - Botão para estender período de teste ou bloquear acesso por inadimplência.
2. **Catálogo de Planos & Preços**:
   - Criação e ajuste de planos, limites de filiais (`max_branches`), utilizadores (`max_users`) e produtos (`max_products`).
3. **Gestão de Entitlements (Feature Flags)**:
   - Ativação sob demanda de verticais especializadas: Módulo Farmácia ANARME, Módulo Restaurante/Mesas, Integração M-Pesa C2B/B2C, Relatórios Avançados com IA.
4. **Logs de Auditoria & Segurança**:
   - Registro de logins administrativos, alterações de plano, provisionamentos e impersonation seguro com expiração de token.

---

## 6. Perfis de Utilizadores e Matriz RBAC

| Role | Escopo | Permissões Típicas |
| :--- | :--- | :--- |
| `super_admin` | **Global Platform** | Acesso ao Control Plane, gestão de planos, criação de tenants e auditoria global. |
| `admin` | **Tenant / Empresa** | Gestão de todas as filiais da empresa, utilizadores, catálogo de produtos, finanças e configurações. |
| `manager` | **Filial Ativa** | Gestão de operações, aprovação de descontos, ajustes de stock e relatórios da sua filial. |
| `cashier` | **Ponto de Venda (POS)** | Abertura/fecho de caixa, emissão de vendas, faturas e recibos térmicos. |
| `stock_manager` | **Armazém / Stock** | Entradas de mercadorias, conferência de lotes, baixas e transferências entre filiais. |
| `staff` | **Operacional Básico** | Visualização de produtos e registro de atendimentos permitidos. |
