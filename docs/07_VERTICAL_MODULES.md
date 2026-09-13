# MÓDULOS VERTICAIS CONFIGURÁVEIS: ZBIZ+
## `docs/07_VERTICAL_MODULES.md`

---

## 1. O CONCEITO DE VERTICAL DRIVE
O ZBIZ+ utiliza `BusinessProfileDrivers` para carregar terminologia, regras de validação e componentes de UI conforme o tipo de negócio:
## 1. O CONCEITO DE VERTICAL DRIVE & ARQUITETURA MULTISSETORIAL
O ZBIZ+ utiliza `BusinessProfileDrivers` dinâmicos (`tenant_theme()`) para adaptar terminologia, esquemas de cores, validações e componentes visuais de acordo com o setor comercial contratado por cada empresa:

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
### Setores Nativamente Suportados:

## 2. Operação Restaurante
1. **Vertical `pharmacy` (Farmácia & Saúde):**
   * **Identidade Visual:** Tom Esmeralda (`#10b981`), ícone `fa-prescription-bottle-medical`.
   * **Campos Clínicos & Regulatórios:** Princípio Ativo (DCI), Forma Farmacêutica (comprimidos, xarope, injetável, pomada), Dosagem (ex: 500mg, 10ml), Lote do Fabricante e Data de Validade.
   * **Controlo Especial:** Medicamentos psicotrópicos, estupefacientes e exigência de retenção de receita médica.
   * **Regra de Dispensa FEFO:** O sistema sugere e dá baixa prioritária no lote cujo vencimento está mais próximo (*First Expired, First Out*).
   * **Serviços Farmacêuticos Integrados:** Suporte nativo a cobrança de serviços de saúde (medição de pressão arterial, injeções, curativos, testes rápidos de malária/glicemia).

### Mesas & Sala
2. **Vertical `restaurant` (Restaurante, Bar & Cafetaria):**
   * **Identidade Visual:** Tom Laranja / Âmbar (`#f97316`), ícone `fa-utensils`.
   * **Gestão de Mesas & Sala (`/restaurant/tables`):** Mapa de mesas com ocupação por filial, capacidade de lugares e estados em tempo real (`Livre`, `Ocupada`, `Reservada`, `Limpeza`).
   * **Comandas & Pedidos:** Abertura de comandas com atribuição de mesa ou venda rápida de balcão / take-away.
   * **Categorização Gastronómica:** Cardápio estruturado por Pratos Principais, Acompanhamentos, Bebidas, Petiscos e Sobremesas.

O menu **Mesas & Sala** está disponível apenas quando o tenant ativo tem o tipo de negócio `restaurant`. Todas as mesas pertencem simultaneamente a um tenant e a uma filial, evitando que uma filial veja ou altere a operação de outra.
3. **Vertical `retail` (Comércio a Retalho, Supermercado & Lojas):**
   * **Identidade Visual:** Tom Azul Celeste (`#0ea5e9`), ícone `fa-cart-shopping`.
   * **Frente de Caixa Ágil (POS 2.0):** Leitura de código de barras por leitor laser/ótico, atalhos de teclado (F1 a F12), talão térmico de 80mm/58mm.
   * **Preços & Promoções:** Preço normal de venda, preço de promoção automático com agendamento de data de expiração, e desconto manual negociado no caixa.
   * **Controlo de Stock Mínimo:** Alertas de rutura de prateleira e reposição de inventário.

Na página de mesas é possível criar uma mesa, indicar a sua capacidade e alterar o estado para livre, ocupada, reservada ou em limpeza.
4. **Vertical `reprography` / `services` (Gráfica, Reprografia & Prestação de Serviços):**
   * **Identidade Visual:** Tom Violeta / Roxo (`#8b5cf6`), ícone `fa-print`.
   * **Venda de Serviços com Insumos:** Associação de folhas, toners, encadernações e plastificações ao serviço prestado.
   * **Controlo de Encomendas:** Gestão de pedidos com prazos de entrega, status de produção, adiantamentos / sinal e liquidação na entrega.

### Comandas e Pedidos
---

Ao criar uma encomenda num tenant restaurante, o utilizador pode escolher uma mesa livre ou reservada. A mesa passa automaticamente para **ocupada** quando a comanda é criada. A opção de mesa é facultativa para pedidos de balcão, entrega ou take-away.
## 2. REVOLUÇÃO: CATÁLOGO UNIVERSAL HÍBRIDO (PRODUTOS + SERVIÇOS)

O isolamento de tenant e filial é validado no servidor; não depende apenas da interface.
Uma das maiores inovações introduzidas na versão `1.0.16` do ZBIZ+ é o **Suporte Universal a Serviços para Todas as Empresas**:

* **Antes:** Apenas tenants do tipo `reprography` podiam registar serviços; farmácias e retalho só podiam cadastrar artigos físicos com controlo rígido de stock.
* **Agora:** Qualquer empresa (Farmácia, Loja, Restaurante, Gráfica) pode registar itens com `type = 'service'`.
  * **Comportamento no Stock:** Serviços não exigem controlo de quantidade física em armazém nem bloqueiam vendas por stock zero.
  * **Comportamento no POS:** Aparecem com selo visual destacado (`badge [Serviço]`) e podem ser faturados no mesmo talão que medicamentos ou mercadorias.
  * **Relatórios e Lucro:** Margem de lucro de 100% (ou calculada sobre materiais consumíveis caso haja insumos associados).

---

## 3. OPERAÇÃO DE RESTAURANTE: MESAS & SALA

### Mapa de Mesas Seguro & Multi-Filial
* Localizado em `/restaurant/tables` e restrito a empresas com `business_type = 'restaurant'`.
* As mesas pertencem simultaneamente a um `tenant_id` e a um `branch_id`, garantindo que uma filial nunca visualize as mesas de outra filial da mesma rede.
* O utilizador pode alterar o estado das mesas diretamente na grelha:
  * 🟢 **Livre:** Pronta para receber novos clientes.
  * 🔴 **Ocupada:** Mesa com clientes ou comanda aberta em consumo.
  * 🟡 **Reservada:** Bloqueada para reserva com horário marcado.
  * 🔵 **Limpeza:** Aguardando higienização após encerramento da conta.

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
