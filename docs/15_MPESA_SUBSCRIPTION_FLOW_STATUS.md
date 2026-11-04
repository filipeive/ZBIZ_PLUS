# Estado da Integração M-Pesa para Pagamento de Subscrições SaaS
## `docs/15_MPESA_SUBSCRIPTION_FLOW_STATUS.md`

---

## 1. Diagnóstico Atual (Fluxo Ponta a Ponta Incompleto)

Após auditoria a `app/Http/Controllers/API/PaymentWebhookController.php`, `app/Services/Payments/Drivers/MpesaDriver.php` e `app/Services/Billing/SubscriptionService.php`, confirma-se que **o pagamento automático de subscrição SaaS via M-Pesa ainda não está ligado de ponta a ponta em produção**:

1. **Iniciação C2B (`POST /api/payments/mpesa/c2b`):**
   - `PaymentWebhookController::initiateMpesa()` apenas invoca `MpesaDriver::initiateC2B()` e devolve a resposta da API Vodacom (`transaction_id`, `conversation_id`, `third_party_ref`).
   - **Não cria** nenhum registo `SubscriptionPayment` com `status = 'pending'` na base de dados nem associa o pedido à `Subscription` do tenant autenticado.
2. **Ativação Manual (`SubscriptionService::subscribe`):**
   - Quando chamado internamente (ex.: ativação administrativa), cria `SubscriptionPayment` diretamente com `status = 'completed'`.
3. **Receção do Callback (`POST /api/webhooks/mpesa`):**
   - `MpesaDriver::processWebhook()` exige (corretamente, por segurança *fail-closed*) que exista previamente um `SubscriptionPayment` com `status = 'pending'`, cujo `mpesa_transaction_id`, `receipt_number` (`third_party_ref`) e `amount` coincidam com o callback da operadora.

---

## 2. O Que Falta Implementar para Ligar o Fluxo Ponta a Ponta

Para concluir o ciclo assíncrono C2B + Webhook de subscrições sem vulnerabilidades de adulteração de preço:

1. **Determinação do Valor no Servidor (Nunca Confiar no `amount` do Cliente):**
   - No endpoint de iniciação de pagamento de subscrição, receber apenas `plan_slug` (ou `plan_id`), periodicidade (`months`: 1 ou 12) e `phone`.
   - Calcular o `amount` exclusivamente no servidor a partir de `Plan::monthly_price` / `Plan::annual_price`.
2. **Criação Atómica de `Subscription` e `SubscriptionPayment` (`pending`) antes/após o STK Push:**
   - Identificar o `tenant_id` a partir do utilizador autenticado (`auth:sanctum` + `tenant`).
   - Gerar `third_party_ref` único no servidor (ex.: `SUB-{tenant_id}-{timestamp}`) e gravá-lo em `SubscriptionPayment.receipt_number`.
   - Invocar `MpesaDriver::initiateC2B($serverCalculatedAmount, $phone, $reference, $thirdPartyRef)`.
   - Persistir `SubscriptionPayment` com:
     - `tenant_id` do contexto autenticado;
     - `subscription_id` da subscrição alvo do tenant;
     - `amount` calculado no servidor;
     - `mpesa_transaction_id` retornado pelo C2B (`output_TransactionID` / `transaction_id`);
     - `receipt_number` igual ao `third_party_ref`;
     - `status = 'pending'`.
3. **Verificação Ativa de Estado (`Query Transaction Status` — OpenAPI Vodacom):**
   - Adicionar método `queryTransactionStatus(string $transactionId, string $thirdPartyRef)` em `MpesaDriver` para confirmar ativamente junto da Vodacom o estado real da transação antes de `processWebhook` transitar `SubscriptionPayment` de `pending` para `completed` e estender `current_period_ends_at` / `subscription_ends_at`.
