# CONSTITUIÇÃO TÉCNICA DO PROJETO: ZBIZ+
## `docs/00_ENGINEERING_GUIDE.md`

---

## 1. PRINCÍPIOS FUNDAMENTAIS DE ENGENHARIA

1. **Simplicidade Pragmática (*KISS*):** Não construir abstrações prematuras ou microservices. O ZBIZ+ é um **Modular Monolith** construído sobre o ecossistema Laravel 12.
2. **Isolamento Absoluto de Dados (*Tenant Isolation*):** Nenhum tenant pode, sob hipótese alguma, consultar, inferir ou alterar dados de outro tenant.
3. **Imutabilidade Financeira (*Ledger First*):** O saldo financeiro nunca é alterado por `UPDATE` arbitrário. Toda e qualquer alteração de saldo exige um registo explícito em `FinancialTransaction` com histórico e snapshot.
4. **Resiliência a Falhas de Conexão (*Offline-First*):** O ponto de venda (POS) deve ser capaz de operar localmente em casos de corte de internet e sincronizar de forma idempotente.
5. **A Verdade Está nos Testes:** Nenhuma funcionalidade é considerada concluída sem testes unitários, testes de integração de multi-tenancy e validação no browser.

---

## 2. PADRÕES DE CÓDIGO E ARQUITETURA LARAVEL

* **Versão PHP:** PHP 8.3+ (tipagem estrita `declare(strict_types=1);` recomendada em novos serviços).
* **PSR:** Conformidade total com PSR-12 e Laravel Pint.
* **Camadas de Responsabilidade:**
  * **Controllers:** Finos (*Thin Controllers*). Apenas recebem o Request, invocam Form Requests para validação e delegam para Actions/Services.
  * **Domain Services:** Lógicas de cálculo (descontos, financeiro, regras fiscais).
  * **Actions:** Classes com método único `execute()` para mutações de estado complexas (ex: `CreateSaleAction`, `ProcessPharmacySaleAction`).
  * **Models:** Apenas relacionamentos, scopes, casts e traits de tenant. Sem regras complexas de persistência direta dentro de hooks ocultos sem evento.

---

## 3. WORKFLOW DE GIT & RELEASES

O repositório `ZBIZ_PLUS` adota um modelo estrito de ramificações:

```text
main         ────────► Produção Estável (Tags semânticas v1.0.0, v1.1.0)
  ▲
develop      ────────► Ambiente de Staging / Integração Contínua
  ▲
feature/*    ────────► Novas funcionalidades (ex: feature/multi-tenancy)
fix/*        ────────► Correções de bugs
refactor/*   ────────► Refatorações estruturais
```

### Regras de Commit:
* Commits semânticos no padrão: `feat:`, `fix:`, `refactor:`, `docs:`, `test:`, `chore:`.
* Nunca realizar push direto na `main`. Todo código entra via Pull Request revisado e testado.

---

## 4. ESTRATÉGIA DE TESTES

### Pirâmide de Testes:
1. **Unit Tests (Pest / PHPUnit):** Cálculos financeiros, rateio de descontos, validação de NUIT, algoritmo FEFO de farmácia.
2. **Feature & Integration Tests:**
   * `TenantIsolationTest`: Cria Tenant A e Tenant B e valida que A recebe 404/vazio ao tentar aceder a recursos de B.
   * `SaleAndStockTest`: Garante que a venda baixa o stock correto por filial e registra a transação financeira correspondente.
3. **Browser Testing (Playwright / Chrome DevTools):**
   * Teste de fluxo completo do POS (Adicionar item → Aplicar desconto → Fechar via M-Pesa).
   * Teste de responsividade (Desktop, Tablet, Mobile).

---

## 5. SEGURANÇA & BOAS PRÁTICAS

* **SQL Injection / Raw Queries:** Proibido uso de queries raw com interpolação de strings. Todo SQL deve utilizar query builder com bindings preparados.
* **Prevenção de IDOR:** Validação explícita de `tenant_id` e `branch_id` em todos os endpoints.
* **Uploads:** Validação de MIME types em storage privado para documentos confidenciais (ex: receitas de psicotrópicos, comprovativos de salário).
* **Rate Limiting:** Proteção de endpoints sensíveis (login, checkout M-Pesa, emissão fiscal) contra força bruta.
