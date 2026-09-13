# ESQUEMA DE BANCO DE DADOS OFICIAL: ZBIZ+ (v1.0.18)
## `docs/06_DATABASE_SCHEMA.md`

---

## 1. VISÃO GERAL & PRINCÍPIOS DE MODELAGEM

O banco de dados do **ZBIZ+** foi arquitetado sob o modelo **Shared Database, Multi-Tenant Partitioning**. Todas as tabelas de domínio contêm chaves de particionamento estrito (`tenant_id` e, onde aplicável, `branch_id`).

### Princípios Mandatórios:
1. **Isolamento de Tenants:** Toda consulta e mutação deve respeitar a trait `BelongsToTenant` e o `TenantScope`.
2. **Isolamento de Filiais (`branch_id`):** O estoque físico, as contas de caixa e as mesas de restaurante pertencem obrigatoriamente a uma filial específica.
3. **Imutabilidade Contábil (Ledger-First):** O saldo financeiro nunca é atualizado diretamente; todas as movimentações geram registos imutáveis em `financial_transactions`.
4. **Catálogo Híbrido Universal:** A tabela `products` suporta produtos com controle físico e serviços sem exigência de quantidade em estoque (`type = 'service'`).

---

## 2. CAMADA SAAS, LICENCIAMENTO & MULTI-BRANCH

### `tenants` (Empresas Clientes da Plataforma)
Representa a conta jurídica ou comercial que contrata o software.
* `id` (BIGINT, PK): Identificador único do tenant.
* `name` (VARCHAR): Razão Social ou Nome Fantasia da empresa.
* `slug` (VARCHAR, UNIQUE): Slug para identificação interna / subdomínio.
* `domain` / `subdomain` (VARCHAR, NULLABLE): Domínio personalizado ou subdomínio SaaS.
* `business_type` (ENUM): Setor de atividade (`pharmacy`, `restaurant`, `retail`, `reprography`, `services`, `other`).
* `nuit` (VARCHAR, NULLABLE): Número Único de Identificação Tributária em Moçambique.
* `email`, `phone`, `address` (VARCHAR): Contatos comerciais da matriz.
* `currency` (VARCHAR): Moeda padrão do tenant (default: `MZN`).
* `status` (ENUM): Estado comercial (`trial`, `active`, `suspended`, `cancelled`).
* `installation_mode` (ENUM): Modo de implantação (`cloud`, `local_online`, `offline`).
* `license_status` (VARCHAR): Situação da licença de software (`active`, `expired`, `suspended`).
* `license_expires_at`, `trial_ends_at`, `subscription_ends_at` (DATETIME): Prazos de vigência.
* `settings` (JSON, NULLABLE): Configurações específicas do tenant (cores, logo, taxas).
* `created_at`, `updated_at` (TIMESTAMP).

### `branches` (Filiais, Lojas & Postos de Atendimento)
* `id` (BIGINT, PK): Identificador único da filial.
* `tenant_id` (BIGINT, FK -> `tenants.id`): Tenant proprietário da filial.
* `name` (VARCHAR): Nome da loja/filial (ex: "Sede Centro", "Posto Bairro Novo").
* `code` (VARCHAR, NULLABLE): Código operacional da filial (ex: `FIL-01`).
* `phone`, `email`, `address` (VARCHAR, NULLABLE): Contatos locais.
* `is_main` (BOOLEAN): Define se é a matriz / filial principal (`true`/`false`).
* `is_active` (BOOLEAN): Estado operacional da filial.
* `created_at`, `updated_at` (TIMESTAMP).

### `plans` (Planos de Subscrição SaaS)
* `id` (BIGINT, PK).
* `name` (VARCHAR): Nome comercial (ex: "ZBIZ Starter", "ZBIZ Business", "ZBIZ Pharmacy+").
* `slug` (VARCHAR, UNIQUE).
* `monthly_price`, `annual_price` (DECIMAL 10,2): Valores em Meticais (MZN).
* `max_branches`, `max_users`, `max_products` (INT): Limites contratados.
* `features` (JSON): Módulos liberados (`pos`, `sales`, `stock_basic`, `pharmacy`, `restaurant`, `salaries`, etc.).
* `is_active` (BOOLEAN), `sort_order` (INT).
* `created_at`, `updated_at` (TIMESTAMP).

### `subscriptions` (Assinaturas Comerciais na Nuvem)
* `id` (BIGINT, PK).
* `tenant_id` (BIGINT, FK -> `tenants.id`).
* `plan_id` (BIGINT, FK -> `plans.id`).
* `status` (VARCHAR): `trialing`, `active`, `suspended`, `cancelled`, `past_due`.
* `trial_starts_at`, `trial_ends_at` (DATETIME).
* `current_period_starts_at`, `current_period_ends_at` (DATETIME).
* `cancelled_at` (DATETIME, NULLABLE).
* `payment_method` (VARCHAR): `mpesa`, `emola`, `manual`, `bank_transfer`.
* `last_payment_reference` (VARCHAR, NULLABLE).
* `created_at`, `updated_at` (TIMESTAMP).

### `license_keys` (Chaves Digitais e Tokens de Licença de Software)
* `id` (BIGINT, PK).
* `tenant_id` (BIGINT, FK -> `tenants.id`).
* `plan_id` (BIGINT, FK -> `plans.id`).
* `issued_by_user_id` (BIGINT, FK -> `users.id`): Super Admin que emitiu a chave.
* `key_code` (VARCHAR): Chave serial legível no formato `ZBIZ-XXXX-XXXX-XXXX-XXXX`.
* `key_hash` (VARCHAR, UNIQUE): Hash identificador único.
* `mode` (VARCHAR): `cloud`, `local_online`, `offline`.
* `status` (ENUM): `issued`, `active`, `revoked`, `expired`.
* `issued_to` (VARCHAR): Nome da entidade licenciada.
* `starts_at`, `expires_at` (DATETIME): Janela de validade.
* `activated_at`, `revoked_at` (DATETIME, NULLABLE).
* `payload` (JSON): Metadados completos do plano, módulos e limites.
* `signature` (TEXT): Assinatura digital criptográfica HMAC SHA-256 para validação offline.
* `notes` (TEXT, NULLABLE).
* `created_at`, `updated_at` (TIMESTAMP).

---

## 3. SEGURANÇA, UTILIZADORES & RECURSOS HUMANOS

### `users` (Colaboradores e Operadores do Sistema)
* `id` (BIGINT, PK).
* `tenant_id` (BIGINT, FK -> `tenants.id`).
* `branch_id` (BIGINT, FK -> `branches.id`, NULLABLE para Super Admins).
* `role_id` (BIGINT, FK -> `roles.id`).
* `name` (VARCHAR), `email` (VARCHAR, UNIQUE no tenant).
* `phone`, `document_number` (VARCHAR, NULLABLE): BI/Passaporte.
* `employee_code` (VARCHAR, NULLABLE): Código de colaborador interno.
* `job_title` (VARCHAR, NULLABLE): Cargo (ex: "Farmacêutico Chefe", "Operador de Caixa").
* `monthly_salary` (DECIMAL 10,2, NULLABLE): Salário base mensal.
* `hire_date` (DATE, NULLABLE): Data de contratação.
* `is_active` (BOOLEAN): Bloqueio/desbloqueio de acesso.
* `photo_path` (VARCHAR, NULLABLE).
* `last_login_at` (DATETIME, NULLABLE).
* `password`, `remember_token`.
* `created_at`, `updated_at` (TIMESTAMP).

### `roles` e `permissions` (Controle de Acessos RBAC - Spatie)
* Suporte nativo a papéis: `super_admin`, `admin`, `manager`, `cashier`, `stock_manager`, `waiter`.

### `user_activities` (Trilha de Auditoria)
* Registo cronológico de ações sensíveis (vendas canceladas, acertos de estoque, fechos de caixa).

---

## 4. CATÁLOGO DE ARTIGOS, SERVIÇOS & VERTICAIS

### `categories` (Classificação de Artigos e Serviços)
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id` (BIGINT, FKs).
* `name`, `description` (VARCHAR).
* `type` (VARCHAR): `products`, `services`, `restaurant`.
* `color`, `icon` (VARCHAR): Para identificação visual no POS.
* `is_active` (BOOLEAN).
* `created_at`, `updated_at` (TIMESTAMP).

### `products` (Produtos Físicos & Serviços Universais)
* `id` (BIGINT, PK).
* `tenant_id` (BIGINT, FK -> `tenants.id`).
* `category_id` (BIGINT, FK -> `categories.id`).
* `linked_product_id` (BIGINT, FK -> `products.id`, NULLABLE): Vinculação de insumo/matéria-prima.
* `name` (VARCHAR): Nome comercial do artigo ou serviço.
* `original_name`, `description` (TEXT, NULLABLE).
* `type` (ENUM):
  * `'product'`: Artigo físico com controle rígido de quantidade e armazém.
  * `'service'`: Serviço prestado (injeções, medição de pressão, testes, consultas, impressões). Não bloqueia por estoque zero.
* `purchase_price` (DECIMAL 10,2): Preço de custo.
* `selling_price` (DECIMAL 10,2): Preço normal de venda ao público.
* `promotional_price` (DECIMAL 10,2, NULLABLE): Preço promocional temporário.
* `is_on_promotion` (BOOLEAN): Ativação de promoção.
* `promotion_discount_percent` (DECIMAL 5,2, NULLABLE).
* `promotion_ends_at` (DATETIME, NULLABLE).
* `stock_quantity` (DECIMAL 12,2): Estoque global do produto.
* `min_stock_level` (DECIMAL 12,2): Limiar para alerta de estoque baixo.
* `unit` (VARCHAR): `un`, `cx`, `kg`, `l`, `srv`.
* `barcode`, `sku` (VARCHAR, NULLABLE): Código de barras e código interno.
* **Campos Especiais de Farmácia (ANARME):**
  * `dosage` (VARCHAR, NULLABLE): Dosagem (ex: "500mg", "10mg/ml").
  * `active_ingredient` (VARCHAR, NULLABLE): Princípio Ativo (DCI - Denominação Comum Internacional).
* `is_active`, `is_deleted` (BOOLEAN), `deleted_at` (TIMESTAMP).
* `created_at`, `updated_at` (TIMESTAMP).

### `product_branches` (Estoque Isolado por Filial)
* `id` (BIGINT, PK).
* `tenant_id`, `product_id`, `branch_id` (BIGINT, FKs).
* `stock_quantity` (DECIMAL 12,2): Estoque específico nesta filial.
* `min_stock_level` (DECIMAL 12,2).
* `location_in_store` (VARCHAR, NULLABLE): Prateleira / corredor.

### `product_batches` (Lotes e Validades de Medicamentos - FEFO)
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id`, `product_id` (BIGINT, FKs).
* `batch_number` (VARCHAR): Código do lote impresso pelo laboratório.
* `expiry_date` (DATE): Data de expiração (base para o algoritmo FEFO e alertas 90/60/30d).
* `manufacture_date` (DATE, NULLABLE).
* `quantity` (DECIMAL 12,2): Quantidade disponível no lote.
* `cost_price` (DECIMAL 10,2): Custo de aquisição do lote.
* `status` (ENUM): `active`, `quarantine`, `expired`, `depleted`.
* `notes` (TEXT, NULLABLE).

### `branch_product_inquiries` (Consulta de Estoque entre Filiais)
* Permite a uma filial sem determinado remédio perguntar em tempo real se outra filial da mesma farmácia tem estoque disponível para transferência ou encaminhamento do paciente.

### `prescriptions` (Controlo de Receitas de Psicotrópicos - ANARME)
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id`, `customer_id` (BIGINT, FKs).
* `patient_name`, `patient_nuit` (VARCHAR).
* `prescriber_name` (VARCHAR): Nome do médico prescritor.
* `prescriber_license` (VARCHAR): Número da carteira profissional da Ordem dos Médicos.
* `health_facility` (VARCHAR): Unidade sanitária de emissão da receita.
* `prescription_date`, `dispensed_at` (DATETIME).
* `notes` (TEXT, NULLABLE).

---

## 5. MÓDULO DE RESTAURAÇÃO: MESAS & SALA

### `restaurant_tables` (Mapa de Mesas da Filial)
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id` (BIGINT, FKs): Garante que mesas nunca vazem entre filiais.
* `name` (VARCHAR): Identificação (ex: "Mesa 01", "Esplanada 04", "VIP 02").
* `capacity` (INT): Número de lugares sentados.
* `status` (ENUM):
  * `'free'`: Livre.
  * `'occupied'`: Ocupada (com comanda aberta).
  * `'reserved'`: Reservada.
  * `'cleaning'`: Em processo de limpeza/higienização.
* `notes` (TEXT, NULLABLE).
* `created_at`, `updated_at` (TIMESTAMP).

---

## 6. VENDAS, PEDIDOS & PONTO DE VENDA (POS 2.0)

### `sales` (Cabeçalho da Venda)
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id` (BIGINT, FKs).
* `user_id` (BIGINT, FK -> `users.id`): Operador de caixa.
* `customer_id` (BIGINT, FK -> `customers.id`, NULLABLE para clientes avulsos).
* `customer_name`, `customer_phone` (VARCHAR, NULLABLE).
* `subtotal` (DECIMAL 10,2): Soma dos preços brutos dos itens.
* `discount_amount` (DECIMAL 10,2): Desconto total concedido.
* `discount_percentage` (DECIMAL 5,2, NULLABLE).
* `discount_type` (ENUM): `none`, `percentage`, `fixed`.
* `discount_reason` (VARCHAR, NULLABLE): Justificativa do desconto.
* `total_amount` (DECIMAL 10,2): Total líquido a liquidar.
* `payment_method` (VARCHAR): `cash`, `mpesa`, `emola`, `pos`, `bank_transfer`, `credit` (fiado), `split`.
* `sale_date` (DATETIME).
* `notes` (TEXT, NULLABLE).

### `sale_items` (Itens Faturados)
* `id` (BIGINT, PK).
* `sale_id` (BIGINT, FK -> `sales.id`).
* `product_id` (BIGINT, FK -> `products.id`).
* `quantity` (DECIMAL 10,2).
* `original_unit_price` (DECIMAL 10,2): Preço antes do desconto.
* `unit_price` (DECIMAL 10,2): Preço praticado.
* `total_price` (DECIMAL 10,2): Valor final da linha.
* `discount_amount`, `discount_percentage`, `discount_type`, `discount_reason`.

### `orders` (Encomendas, Comandas & Ordens de Produção)
* Suporta pedidos com sinal/adiantamento, entregas futuras e associação com mesas de restaurante (`restaurant_table_id`).

---

## 7. MOTOR FINANCEIRO & LIVRO-RAZÃO (LEDGER FIRST)

### `financial_accounts` (Contas Financeiras e Caixas)
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id` (BIGINT, FKs).
* `name` (VARCHAR): Ex: "Caixa Balcão 1", "M-Pesa Loja", "Conta BCI".
* `slug` (VARCHAR).
* `type` (ENUM): `cash`, `bank`, `mobile_money` (M-Pesa/e-Mola).
* `opening_balance` (DECIMAL 12,2): Saldo inicial.
* `current_balance` (DECIMAL 12,2): Saldo consolidado através das transações.
* `is_active` (BOOLEAN), `sort_order` (INT).

### `financial_transactions` (Livro-Razão Imutável)
Cada entrada ou saída de dinheiro no sistema gera obrigatoriamente um registo aqui:
* `id` (BIGINT, PK).
* `tenant_id`, `branch_id` (BIGINT, FKs).
* `financial_account_id` (BIGINT, FK -> `financial_accounts.id`).
* `user_id` (BIGINT, FK -> `users.id`): Responsável pelo lançamento.
* `type` (VARCHAR): `sale`, `expense`, `debt_payment`, `salary_payment`, `transfer`, `adjustment`.
* `direction` (ENUM): `in` (Entrada de Dinheiro) ou `out` (Saída de Dinheiro).
* `amount` (DECIMAL 12,2): Valor da transação.
* `balance_after` (DECIMAL 12,2): Snapshot do saldo da conta após a transação.
* `transaction_date` (DATETIME).
* `description` (VARCHAR): Histórico detalhado.
* `reference_type`, `reference_id`: Polimorfismo vinculando a venda, despesa ou recibo.
* `status` (ENUM): `completed`, `pending`, `reversed`.
* `reversed_by`, `reversal_of`: Auditoria de estornos.
* `include_in_metrics` (BOOLEAN): Filtro para cálculos DRE/Dashboard.

### `cash_shifts` (Controlo de Turnos de Caixa)
* Abertura com fundo de maneio (`opening_balance`).
* Fecho com contagem cega do operador (`closing_balance_actual`) e confronto com o sistema (`closing_balance_system`), apurando sobras ou quebras de caixa (`difference`).

---

## 8. DÍVIDAS, DESPESAS & FOLHA DE PAGAMENTO

* `debts`: Registo de vendas a fiado e dívidas de clientes/funcionários, com limite de crédito e valor remanescente (`remaining_amount`).
* `debt_payments`: Quitações parciais ou totais de dívidas com emissão de recibo.
* `expenses` & `expense_categories`: Gestão de despesas operacionais (rendas, energia, manutenção) e emissão de Recibos de Renda.
* `salary_payments`: Pagamento de remunerações com recibo detalhado de salário.

---

## 9. LOGÍSTICA & ESTOQUE

* `stock_movements`: Registo de todas as movimentações físicas (entradas por compra, saídas por venda, perdas por quebra, acertos de inventário).
* `stock_transfers`: Guias de transferência física de mercadorias entre filiais da mesma rede.

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
