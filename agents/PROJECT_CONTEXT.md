# CONTEXTO RÁPIDO DO PROJETO ZBIZ+ PARA AGENTES
## `agents/PROJECT_CONTEXT.md`

### 1. O que é o ZBIZ+? (Versão Atual 1.0.21)
Uma plataforma SaaS modular e multi-tenant (ERP + POS) para Moçambique, atendendo diversos setores:
* **ZBIZ Core:** Núcleo multi-tenant com isolamento estrito (`BelongsToTenant`), filiais independentes, funcionários, contas e transações.
* **Autenticação Flexível:** Login por E-mail, Nome de Usuário, Telemóvel (nacional/internacional) ou Código de Funcionário. Recuperação de senha por E-mail ou SMS com código OTP de 6 dígitos.
* **Resiliência Arquitetural:** Driver de sessão e cache em `file` para alta velocidade local e proteção contra quedas de banco; manipulador global em `bootstrap/app.php` com tela amigável de auto-recuperação (`errors.database`).
* **ZBIZ Owner Control Center (`/owner/tenants`):** Cockpit do Super Admin com métricas SaaS (MRR/ARR), emissão de chaves seriais criptografadas (`ZBIZ-XXXX-...`), ciclo de vida de licenças (Reactivar, Arquivar via Soft Delete, Restaurar), certificados oficiais PDF e acesso de suporte (*impersonate*).
* **ZBIZ POS 2.0:** Frente de caixa rápida, suporte a leitor de código de barras, atalhos de teclado, descontos promocionais/manuais, compatibilidade nativa com subcaminhos de produção (`/zbiz_plus`) e operação offline-first com fila em localStorage e sincronização retroativa.
* **ZBIZ Pharmacy:** Gestão farmacêutica com Lotes, Validades em 3 níveis (30/60/90 dias), saída FEFO, controlo de psicotrópicos e enquadramento regulatório ANARME.
* **ZBIZ Restaurant:** Gestão de Mesas & Sala (`/restaurant/tables`), estados de ocupação e comandas.
* **ZBIZ Universal Catalog:** Venda de produtos físicos e serviços universais para qualquer setor (farmácias, gráficas, oficinas, lojas).
* **ZBIZ Finance:** Livro-Razão imutável, Dívidas/Fiados por cliente, Despesas, Salários e pagamentos M-Pesa / e-Mola.

### 2. Principais Diretórios:
* `app/Models/` — Modelos Eloquent com `BelongsToTenant` e `SoftDeletes`
* `app/Services/` — Serviços de domínio (`FinancialLedgerService`, `LicenseService`, `DiscountService`, `SmsService`)
* `app/Http/Controllers/` — Controladores finos segregados por domínio
* `resources/views/` — Blade templates integrados com TailwindCSS e Alpine.js
* `resources/views/errors/` — Telas especializadas de erro e recuperação de conexão (`database.blade.php`, `500.blade.php`)
* `docs/` — Documentação técnica viva, roadmap e apresentações comerciais
* `tests/` — Testes automatizados Pest / PHPUnit

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.21*  
────────────────────────────────────────────────────────────────────────────  
