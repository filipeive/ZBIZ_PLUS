# MÓDULOS VERTICAIS CONFIGURÁVEIS: ZBIZ+
## `docs/07_VERTICAL_MODULES.md`

---

## 1. O CONCEITO DE VERTICAL DRIVE
O ZBIZ+ utiliza `BusinessProfileDrivers` para carregar terminologia, regras de validação e componentes de UI conforme o tipo de negócio:

### Verticais Suportados:
1. **Vertical `retail` (Comércio / Lojas):**
   * Campos ativos: Código de barras, referências, tamanhos/cores, alertas de stock mínimo.
2. **Vertical `pharmacy` (Farmácias / Drogarias):**
   * Campos ativos: Princípio ativo (DCI), forma farmacêutica, dosagem, lotes, validade, controlo de psicotrópicos.
3. **Vertical `services` / `reprography` (Gráficas / Serviços):**
   * Campos ativos: Insumos vinculados, ordens de trabalho, prazos de entrega, sinal/adiantamento.
4. **Vertical `restaurant` (Restauração / Cafés - Em implementação):**
   * Categorias padrão para pratos, bebidas, petiscos e cafetaria.
   * Gestão de mesas por tenant e filial, com estados livre, ocupada, reservada e limpeza.
   * Associação opcional de uma mesa às encomendas/comandas existentes.
   * Acesso dedicado a `Mesas & Sala` no sidebar apenas para tenants restaurante.
   * Próxima evolução: comandos de cozinha, complementos e ingredientes.

## 2. Operação Restaurante

### Mesas & Sala

O menu **Mesas & Sala** está disponível apenas quando o tenant ativo tem o tipo de negócio `restaurant`. Todas as mesas pertencem simultaneamente a um tenant e a uma filial, evitando que uma filial veja ou altere a operação de outra.

Na página de mesas é possível criar uma mesa, indicar a sua capacidade e alterar o estado para livre, ocupada, reservada ou em limpeza.

### Comandas e Pedidos

Ao criar uma encomenda num tenant restaurante, o utilizador pode escolher uma mesa livre ou reservada. A mesa passa automaticamente para **ocupada** quando a comanda é criada. A opção de mesa é facultativa para pedidos de balcão, entrega ou take-away.

O isolamento de tenant e filial é validado no servidor; não depende apenas da interface.
