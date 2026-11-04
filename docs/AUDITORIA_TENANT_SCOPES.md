# Auditoria de `withoutGlobalScopes()` — Isolamento Multi-Tenant ZBIZ+

## Resumo Executivo
Esta auditoria inventaria todas as ocorrências de `withoutGlobalScopes()` em `app/` e `routes/`.  
**Regra de Governança:** Nenhuma consulta de negócio pode utilizar `withoutGlobalScopes()` sem aplicar um filtro explícito de `where('tenant_id', ...)` do tenant autenticado/resolvido, exceto nos fluxos de pré-autenticação (login/OTP), validação criptográfica de chave de licença ou listagem de tenants em comando CLI de sincronização.

---

## Inventário Completo de Ocorrências

| # | Ficheiro | Linha | Entidade | Motivo / Contexto | Filtro Explícito `tenant_id`? | Estado |
| :- | :--- | :-: | :--- | :--- | :--- | :-: |
| 1 | `app/Actions/CashShifts/CloseBlindShiftAction.php` | 19 | `CashShift` | Lock pessimista (`lockForUpdate`) do turno em fecho cego | **Sim** (`->where('tenant_id', $shift->tenant_id)->whereKey($shift->id)`) | Seguro |
| 2 | `app/Actions/Sales/CreateSaleFromPosAction.php` | 43 | `Sale` | Verificação de idempotência prévia por `offline_id` no checkout POS | **Sim** (`->where('tenant_id', $tenantId)->where('offline_id', ...)`) | Seguro |
| 3 | `app/Actions/Sales/CreateSaleFromPosAction.php` | 278 | `Sale` | Recuperação idempotente após `UniqueConstraintViolationException` por `offline_id` | **Sim** (`->where('tenant_id', $tenantId)->where('offline_id', ...)`) | Seguro |
| 4 | `app/Models/Sale.php` | 333 | `Sale` | Geração sequencial anual de fatura (`FR`/`FT`/`FP`) com `lockForUpdate` | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 5 | `app/Models/Quotation.php` | 93 | `Quotation` | Geração sequencial anual de cotação (`COT-YYYY/XXXX`) | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 6 | `app/Services/Payments/Drivers/MpesaDriver.php` | 173 | `SubscriptionPayment` | Callback webhook da Vodacom M-Pesa (sem sessão de utilizador) | **Sim** (validado por `mpesa_transaction_id` + `receipt_number`/`third_party_ref` + `amount` + `VerifyMpesaWebhook`) | Seguro |
| 7 | `app/Http/Requests/Auth/LoginRequest.php` | 65 | `User` | Resolução pré-login por email, username, telemóvel ou código de colaborador | **Legítimo Pré-Auth** (prioriza `current_tenant_id()` quando presente e exige `Hash::check`) | Seguro |
| 8 | `app/Http/Controllers/Auth/PasswordResetLinkController.php` | 72 | `User` | Solicitação de código SMS OTP por número de telemóvel | **Legítimo Pré-Auth** (`->when($contextTenantId, fn ($q) => $q->where('tenant_id', $contextTenantId))`) | Seguro |
| 9 | `app/Http/Controllers/Auth/PasswordResetLinkController.php` | 146 | `User` | Redefinição de senha após validação de OTP de 6 dígitos em cache | **Legítimo Pré-Auth** (`->whereKey((int) $cachedData['user_id'])` vinculado ao OTP validado) | Seguro |
| 10 | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` | 27 | `Tenant` | Exibição do branding local no ecrã de login em modo `INSTALLATION_MODE=offline` | **Legítimo Pré-Auth** (restrito ao modo monomáquina `offline`, apenas leitura de branding) | Seguro |
| 11 | `app/Http/Controllers/AdminController.php` | 42 | `Sale` | Contagem de vendas locais pendentes de sincronização nas Definições | **Sim** (`->where('tenant_id', $tenant?->id)`) | Seguro |
| 12 | `app/Http/Controllers/AdminController.php` | 46 | `StockMovement` | Contagem de movimentos locais pendentes de sincronização nas Definições | **Sim** (`->where('tenant_id', $tenant?->id)`) | Seguro |
| 13 | `app/Http/Controllers/AdminController.php` | 50 | `Category` | Contagem de categorias locais na aba de sincronização | **Sim** (`->where('tenant_id', $tenant?->id)`) | Seguro |
| 14 | `app/Http/Controllers/AdminController.php` | 53 | `Product` | Contagem de produtos locais na aba de sincronização | **Sim** (`->where('tenant_id', $tenant?->id)`) | Seguro |
| 15 | `app/Http/Controllers/AdminController.php` | 56 | `LicenseKey` | Leitura da chave de licença ativa do tenant nas Definições | **Sim** (`->where('tenant_id', $tenant?->id)`) | Seguro |
| 16 | `app/Http/Controllers/DebtController.php` | 45 | `Sale` | Eager loading da venda associada à dívida na listagem | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 17 | `app/Http/Controllers/ProductController.php` | 64, 67, 70 | `Category`, `Product` | Listagem e contadores de produtos no catálogo web | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 18 | `app/Http/Controllers/ProductController.php` | 87, 89 | `Category`, `Product` | Formulário de criação de produto (`create`) | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 19 | `app/Http/Controllers/ProductController.php` | 189, 190 | `Category`, `Product` | Formulário de edição de produto (`edit`) | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 20 | `app/Http/Controllers/ProductController.php` | 569, 570, 571 | `Category`, `Product` | Filtro AJAX de produtos (`filter`) | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 21 | `app/Http/Controllers/QuotationController.php` | 78 | `Product` | Carregamento de produtos ativos no formulário de cotação | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 22 | `app/Http/Controllers/ReportController.php` | 917 | `Sale` | Relatório especializado de vendas (`salesReport`) | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 23 | `app/Http/Controllers/SaleController.php` | 94, 110 | `Product` | Carregamento de produtos em `create` e `manualCreate` | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 24 | `app/Http/Controllers/StockMovementController.php` | 17, 170, 196 | `StockMovement` | Listagem de movimentos e cálculo de saldo inicial/Kardex por produto | **Sim** (`->where('tenant_id', $tenantId)`) | Seguro |
| 25 | `app/Http/Controllers/Api/SyncIngestController.php` | 36, 122, 686 | `LicenseKey` | Resolução de licença por `key_code`/`key_hash` nos endpoints de sync e backup | **Legítimo Auth Sync** (autentica a chave e fixa `$licenseTenantId`) | Seguro |
| 26 | `app/Http/Controllers/Api/SyncIngestController.php` | 170, 189, 192, 195 | `Tenant` | Resolução do Tenant alvo (com bloqueio 403 se divergir de `$licenseTenantId`) | **Sim** (validado contra `$licenseTenantId`) | Seguro |
| 27 | `app/Http/Controllers/Api/SyncIngestController.php` | 208, 211, 221, 246, 253, 325–596 | `Branch`, `User`, `Category`, `Product`, `Sale`, `StockMovement` | Ingestão idempotente de dados sincronizados para o tenant autenticado | **Sim** (`->where('tenant_id', $resolvedTenantId)`) | Seguro |
| 28 | `app/Console/Commands/SyncPushCommand.php` | 35, 37, 38 | `Tenant` | Seleção de tenants ativos no comando CLI `zbiz:sync-push` | **Legítimo CLI** (itera cada `$tenant` isoladamente) | Seguro |
| 29 | `app/Console/Commands/SyncPushCommand.php` | 54, 67, 89, 98, 236, 242, 265 | `Category`, `Product`, `Sale`, `StockMovement`, `LicenseKey` | Coleta e marcação de registos sincronizados por tenant no CLI | **Sim** (`->where('tenant_id', $tenant->id)`) | Seguro |
