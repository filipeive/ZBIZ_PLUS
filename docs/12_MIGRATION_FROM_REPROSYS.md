# PLANO DE MIGRAÇÃO: REPROSYS → ZBIZ+
## `docs/12_MIGRATION_FROM_REPROSYS.md`

---

## 1. PRINCÍPIO DE NÃO DESTRUIÇÃO
O ReproSys permanece intacto no seu repositório original. A migração de clientes e histórico será feita através de comandos de importação dedicados.

## 2. COMANDO DE IMPORTAÇÃO
* `php artisan zbiz:import-reprosys --database=reprosys_db --tenant=1`
* Importa em ordem relacional estrita:
  1. Usuários e Papéis
  2. Categorias e Produtos (com vínculo de insumos preservado)
  3. Contas Financeiras e Saldo de Abertura
  4. Vendas Históricas e Itens
  5. Dívidas Ativas e Histórico de Pagamentos
  6. Despesas e Categorias Operacionais
