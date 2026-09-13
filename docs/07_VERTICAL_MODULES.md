# MÓDULOS VERTICAIS CONFIGURÁVEIS: ZBIZ+
## `docs/07_VERTICAL_MODULES.md`

---

## 1. O CONCEITO DE VERTICAL DRIVE & ARQUITETURA MULTISSETORIAL
O ZBIZ+ utiliza `BusinessProfileDrivers` dinâmicos (`tenant_theme()`) para adaptar terminologia, esquemas de cores, validações e componentes visuais de acordo com o setor comercial contratado por cada empresa:

### Setores Nativamente Suportados:

1. **Vertical `pharmacy` (Farmácia & Saúde):**
   * **Identidade Visual:** Tom Esmeralda (`#10b981`), ícone `fa-prescription-bottle-medical`.
   * **Campos Clínicos & Regulatórios:** Princípio Ativo (DCI), Forma Farmacêutica (comprimidos, xarope, injetável, pomada), Dosagem (ex: 500mg, 10ml), Lote do Fabricante e Data de Validade.
   * **Controlo Especial:** Medicamentos psicotrópicos, estupefacientes e exigência de retenção de receita médica.
   * **Regra de Dispensa FEFO:** O sistema sugere e dá baixa prioritária no lote cujo vencimento está mais próximo (*First Expired, First Out*).
   * **Serviços Farmacêuticos Integrados:** Suporte nativo a cobrança de serviços de saúde (medição de pressão arterial, injeções, curativos, testes rápidos de malária/glicemia).

2. **Vertical `restaurant` (Restaurante, Bar & Cafetaria):**
   * **Identidade Visual:** Tom Laranja / Âmbar (`#f97316`), ícone `fa-utensils`.
   * **Gestão de Mesas & Sala (`/restaurant/tables`):** Mapa de mesas com ocupação por filial, capacidade de lugares e estados em tempo real (`Livre`, `Ocupada`, `Reservada`, `Limpeza`).
   * **Comandas & Pedidos:** Abertura de comandas com atribuição de mesa ou venda rápida de balcão / take-away.
   * **Categorização Gastronómica:** Cardápio estruturado por Pratos Principais, Acompanhamentos, Bebidas, Petiscos e Sobremesas.

3. **Vertical `retail` (Comércio a Retalho, Supermercado & Lojas):**
   * **Identidade Visual:** Tom Azul Celeste (`#0ea5e9`), ícone `fa-cart-shopping`.
   * **Frente de Caixa Ágil (POS 2.0):** Leitura de código de barras por leitor laser/ótico, atalhos de teclado (F1 a F12), talão térmico de 80mm/58mm.
   * **Preços & Promoções:** Preço normal de venda, preço de promoção automático com agendamento de data de expiração, e desconto manual negociado no caixa.
   * **Controlo de Stock Mínimo:** Alertas de rutura de prateleira e reposição de inventário.

4. **Vertical `reprography` / `services` (Gráfica, Reprografia & Prestação de Serviços):**
   * **Identidade Visual:** Tom Violeta / Roxo (`#8b5cf6`), ícone `fa-print`.
   * **Venda de Serviços com Insumos:** Associação de folhas, toners, encadernações e plastificações ao serviço prestado.
   * **Controlo de Encomendas:** Gestão de pedidos com prazos de entrega, status de produção, adiantamentos / sinal e liquidação na entrega.

---

## 2. REVOLUÇÃO: CATÁLOGO UNIVERSAL HÍBRIDO (PRODUTOS + SERVIÇOS)

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
**Desenvolvido por: fdev-ms (FDS Multiservices)**  
*Engenharia de Software & Suporte Técnico:* `fdev-ms@fdevms:~/Filipe/reprosys$`  
*Contacto & Assistência:* (+258) 84 999 1122 · Quelimane / Moçambique  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
