# ESQUEMA DE BANCO DE DADOS: ZBIZ+
## `docs/06_DATABASE_SCHEMA.md`

---

## 1. TABELAS DE GESTÃO SAAS
* `tenants` (id, name, slug, domain, business_type, plan_id, status, trial_ends_at, created_at)
* `branches` (id, tenant_id, name, code, phone, address, is_main, is_active)
* `subscriptions` (id, tenant_id, plan_id, status, starts_at, ends_at, mpesa_reference)
* `plans` (id, name, slug, monthly_price, max_branches, max_users, features_json)

## 2. TABELAS DO CORE EMPRESARIAL
* `users` (id, tenant_id, branch_id, name, email, phone, role_id, is_active)
* `customers` (id, tenant_id, name, phone, email, document_number, credit_limit, notes)
* `suppliers` (id, tenant_id, name, phone, email, document_number, address)
* `categories` (id, tenant_id, name, slug, is_active)
* `products` (id, tenant_id, category_id, name, type, purchase_price, selling_price, barcode, is_active)
* `product_branches` (id, tenant_id, product_id, branch_id, stock_quantity, min_stock_level)
* `stock_movements` (id, tenant_id, branch_id, product_id, user_id, type, quantity, reason)

## 3. TABELAS FINANCEIRAS & VENDAS
* `financial_accounts` (id, tenant_id, branch_id, name, slug, type, current_balance, is_active)
* `financial_transactions` (id, tenant_id, branch_id, financial_account_id, user_id, type, direction, amount, balance_after, status)
* `sales` (id, tenant_id, branch_id, user_id, customer_id, subtotal, discount_amount, total_amount, payment_method, sale_date)
* `sale_items` (id, sale_id, product_id, product_batch_id, quantity, unit_price, discount_amount, total_price)
* `debts` (id, tenant_id, branch_id, customer_id, original_amount, remaining_amount, due_date, status)
* `debt_payments` (id, debt_id, user_id, amount, payment_method, payment_date)

## 4. TABELAS VERTICAIS (FARMÁCIA)
* `product_batches` (id, tenant_id, product_id, branch_id, batch_number, expiration_date, quantity, cost_price)
* `branch_product_inquiries` (id, tenant_id, branch_id, sender_branch_id, recipient_branch_id, product_id, product_name, user_id, quantity, message, status, response, response_by, read_at, timestamps)
* `prescription_records` (id, tenant_id, branch_id, sale_id, doctor_name, doctor_reg_number, prescription_photo_path)
