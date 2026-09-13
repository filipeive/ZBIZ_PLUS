# GUIA REGULATÓRIO DE FARMÁCIA (ANARME)
# GUIA REGULATÓRIO & OPERACIONAL DE FARMÁCIA (ANARME / MISAU)
## `docs/09_PHARMACY_REGULATORY_GUIDE.md`

---

## 1. ENQUADRAMENTO LEGAL EM MOÇAMBIQUE
* Regulamentado pela **ANARME** (Agência Nacional Reguladora de Medicamentos) — Decreto n.º 84/2021.
* Exigência de rastreabilidade completa por lote e prazo de validade.

## 2. REQUISITOS TÉCNICOS NO ZBIZ PHARMACY
1. **Alertas de Validade em 3 Níveis:**
   * Amarelo: Medicamentos a vencer em 90 dias.
   * Laranja: Medicamentos a vencer em 60 dias.
   * Vermelho / Bloqueado: Medicamentos a vencer em 30 dias ou vencidos.
2. **Saída FEFO (*First Expired, First Out*):** O POS sugere automaticamente a baixa do lote que expira primeiro.
3. **Livro de Psicotrópicos & Estupefacientes:** Registo de número de receita, médico prescritor e upload da imagem da prescrição.
A atividade farmacêutica em Moçambique é estritamente regulamentada pela **ANARME** (Agência Nacional Reguladora de Medicamentos — Decreto n.º 84/2021) e pelas directrizes do **Ministério da Saúde (MISAU)**.

As farmácias privadas e postos de medicamentos têm obrigações mandatórias de:
1. **Rastreabilidade Total:** Saber a origem, lote, data de fabrico e expiração de cada medicamento dispensado.
2. **Prevenção de Medicamentos Vencidos:** É expressamente proibida a comercialização ou manutenção de produtos expirados em áreas de venda.
3. **Controlo de Substâncias Psicotrópicas & Estupefacientes:** Retenção obrigatória de receitas médicas, registo do médico prescritor e livro oficial de entradas e saídas.
4. **Identificação por DCI (Denominação Comum Internacional):** Obrigatoriedade de pesquisa e referência por Princípio Ativo (ex: Paracetamol, Ibuprofeno, Amoxicilina), e não apenas pela marca comercial.

---

## 2. MECANISMOS NATIVOS DO ZBIZ+ PHARMACY

O **ZBIZ+ Pharmacy** foi desenhado especificamente para blindar a farmácia contra multas e infrações regulatórias:

### A. Triplo Nível de Alerta de Validades (Prevenção de Perdas Financeiras)
O sistema calcula diariamente a distância para a data de expiração de todos os lotes em prateleira e armazém:
* 🟡 **Alerta Amarelo (90 dias):** Medicamento entra em atenção no relatório de inventário e sugestão para campanhas de rotação rápida.
* 🟠 **Alerta Laranja (60 dias):** Notificação em destaque na tela inicial do gestor; indicação para priorizar em dispensação rápida ou devolução ao fornecedor/distribuidor.
* 🔴 **Alerta Vermelho / Bloqueio (30 dias ou Expirado):** O sistema alerta o operador de caixa e bloqueia a saída normal de itens vencidos, sinalizando a necessidade de quarentena.

### B. Algoritmo FEFO Automático (*First Expired, First Out*)
* Quando um medicamento possui múltiplos lotes cadastrados (ex: Lote A vence em Novembro de 2026 e Lote B vence em Março de 2027), o ponto de venda (POS) seleciona automaticamente o **Lote A**.
* Evita que medicamentos mais antigos fiquem esquecidos no fundo da prateleira até se perderem.

### C. Controlo Rigoroso de Psicotrópicos & Medicamentos Sujeitos a Receita
* Sinalização no cadastro do artigo: `requires_prescription = true` ou `is_psychotropic = true`.
* No momento da venda, o sistema solicita:
  * Nome do Médico Prescritor e Número da Cédula Profissional;
  * Número de Registo da Receita Médica;
  * Identificação do Paciente / Comprador.
* Geração do **Relatório Oficial de Psicotrópicos** pronto para fiscalização da ANARME.

### D. Prestação de Serviços Farmacêuticos
As farmácias moçambicanas prestam cuidados primários de saúde essenciais à comunidade. O ZBIZ+ permite a cobrança e registo integrado de:
* Injeções e Administração de Medicamentos;
* Medição de Pressão Arterial (Tensão);
* Testes Rápidos (Glicemia, Malária, HIV);
* Curativos e Primeiros Socorros;
* Pesagem e Orientação Nutricional.

Tudo é faturado diretamente no POS sem exigir baixa física de inventário, integrando o caixa geral.

---

## 3. RELATÓRIOS PARA A DIRECÇÃO TÉCNICA
* **Mapa de Vencimentos por Período:** Listagem exportável para planeamento de compras.
* **Histórico de Movimentação por Lote:** Permite responder a recolhas de mercado (*recalls*) da ANARME em segundos.
* **Inventário Físico vs Teórico por Lotes e Validades.**

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
