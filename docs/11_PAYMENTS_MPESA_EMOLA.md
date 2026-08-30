# INTEGRAÇÃO DE PAGAMENTOS: M-PESA & E-MOLA
## `docs/11_PAYMENTS_MPESA_EMOLA.md`

---

## 1. MODELO DE PAGAMENTO M-PESA (C2B STK PUSH)
1. O operador do POS ou o cliente insere o número Vodacom (84xxxxxxx / 85xxxxxxx).
2. O ZBIZ+ envia requisição para a API oficial do Vodacom M-Pesa.
3. O telemóvel do cliente recebe um popup solicitando o PIN M-Pesa.
4. O servidor recebe a notificação webhook (C2B Confirmation) e liquida a venda instantaneamente.

## 2. MODO MANUAL DE CONTINGÊNCIA
* Possibilidade de selecionar "M-Pesa Manual" e registrar o código de transação alfanumérico recebido por SMS para conferência posterior.
