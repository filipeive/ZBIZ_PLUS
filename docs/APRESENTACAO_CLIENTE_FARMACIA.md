# ZBIZ+ PHARMACY: SISTEMA INTEGRADO DE GESTÃO FARMACÊUTICA & POS
## Apresentação Comercial & Manual de Operação para Farmácias e Drogarias
### `docs/APRESENTACAO_CLIENTE_FARMACIA.md`

---

## 🏥 1. O QUE É O ZBIZ+ PHARMACY?

O **ZBIZ+ Pharmacy** é um sistema completo e moderno de gestão farmacêutica, frente de caixa (POS 2.0), controlo de inventário e conformidade fiscal/regulatória, desenvolvido especificamente para atender à realidade prática e às exigências legais de **Moçambique**.

Ele foi desenhado para proprietários de farmácias, diretores técnicos, farmacêuticos e operadores de caixa que precisam de **agilidade no balcão, segurança máxima contra desvios de caixa, controlo rigoroso de validades (ANARME), gestão de fornecedores e prestação de contas fiscais transparentes (Autoridade Tributária - CIVA)**.

---

## 🎯 2. OS GRANDES PROBLEMAS QUE O ZBIZ+ RESOLVE NA SUA FARMÁCIA

| Desafio Comum na Farmácia | Como o ZBIZ+ Pharmacy Resolve |
| :--- | :--- |
| **Medicamentos vencidos na prateleira** | **Radar de Validades Automático (90, 60 e 30 dias)** e dispensa inteligente **FEFO** (*First Expired, First Out - o lote que vence primeiro é vendido primeiro*). |
| **Filas longas e demora no atendimento** | **Frente de Caixa (POS 2.0) ultra-rápido**, pesquisa por nome comercial ou Princípio Ativo (DCI), cadastro rápido de clientes sem sair da venda e compatibilidade com leitores de código de barras. |
| **Diferenças de dinheiro e desvios de caixa** | **Controlo de turnos com Fecho Cego Anti-Fraude (Blind Closing)**: o operador conta o dinheiro físico sem ver o saldo do sistema antes, apurando quebras/sobras com emissão imediata do Talão Z (80mm). |
| **Medo de multas da fiscalização da ANARME** | **Rastreabilidade obrigatória por Lote e Validade**, com registo de médico prescritor, cédula e receita médica para medicamentos psicotrópicos e controlados. |
| **Gestão de Fornecedores e Reposição** | **Vínculo Fornecedor-Medicamento**: associação direta do distribuidor a cada produto, histórico de cotações, compras e controle de estado ativo/inativo em 1 clique. |
| **Prestação de Contas à Autoridade Tributária (AT)** | **Mapa Fiscal de Apuramento de IVA (Modelo A)**: separação automática de medicamentos isentos (**Artigo 9º do CIVA**) e produtos tributados a **16%**, com exportação formal em PDF A4. |
| **Internet que falha constantemente** | **Modo 100% Offline Garantido**, operando na rede local com ativação por Chave de Licença Serial Digital (`ZBIZ-XXXX-...`). |
| **Perda de dinheiro com vendas a fiado** | **Módulo de Dívidas Completo**: limite de crédito por cliente, extrato individual e emissão de **Comprovativo Térmico Oficial de Amortização (80mm)**. |
| **Serviços clínicos não cobrados** | **Cobrança integrada de serviços de saúde** (injeções, medição de tensão, curativos, testes rápidos de glicemia/malária) sem interferir no stock físico. |
| **Risco de perda de dados e avarias** | **Gestão de Backups Integrada**: geração de cópias de segurança completas da base de dados (SQL) sob demanda para download e salvaguarda externa. |

---

## 💎 3. AS 10 PRINCIPAIS FUNCIONALIDADES DO SISTEMA

### 1. ⚡ Frente de Caixa (POS 2.0) Especializado para Balcão
* **Pesquisa Instantânea por DCI / Princípio Ativo:** Se o cliente pedir *Paracetamol*, o sistema lista imediatamente todas as marcas disponíveis na prateleira (Panadol, Empar, Paracetamol genérico) com preço, lote e quantidade em tempo real.
* **Leitura Ótica por Código de Barras & Atalhos Rápidos:** Teclas funcionais F2 (pesquisa), F4 (cliente) e F9 (finalizar pagamento).
* **Cadastro Rápido de Clientes no Balcão:** O operador clica em `+ Novo Cliente`, regista nome, telefone (M-Pesa) e NUIT em segundos, e o cliente já entra selecionado no carrinho.
* **Impressão Térmica de Talões 80mm com Logótipo:** Talão fiscal formatado com logótipo oficial da farmácia, dados do estabelecimento, discriminação de lotes, IVA e troco.

### 2. 🛡️ Gestão de Lotes & Algoritmo FEFO (*First Expired, First Out*)
* Cada entrada de medicamento é associada ao seu **número de lote** e **data de validade**.
* Quando o operador adiciona um produto ao carrinho no POS, **o sistema seleciona automaticamente o lote com vencimento mais próximo**, impedindo que caixas antigas fiquem esquecidas no fundo da gaveta.

### 3. 🚨 Radar Preventivo de Validades em 3 Níveis
O painel inicial avisa a equipa com antecedência, evitando perdas financeiras:
* 🟡 **Alerta Amarelo (90 dias):** Planeie promoções preventivas ou rotação de stock.
* 🟠 **Alerta Laranja (60 dias):** Solicite a troca ou devolução junto do distribuidor/fornecedor cadastrado.
* 🔴 **Alerta Vermelho / Bloqueio (30 dias ou Expirado):** O produto é sinalizado com urgência e bloqueado para evitar a venda inadvertida de fármacos vencidos ao público.

### 4. 🔒 Turnos de Caixa & Fecho Cego (Blind Closing Anti-Fraude)
* **Abertura de Turno:** O operador regista o fundo de maneio inicial (troco na gaveta).
* **Fecho Cego Z:** O operador insere a contagem física real do numerário na gaveta (com calculadora rápida de notas em Meticais: 1.000, 500, 200, 100, 50, 20 MT e moedas) **sem que o sistema informe previamente o saldo esperado**.
* **Auditoria de Quebras/Sobras:** O sistema compara a contagem com as vendas registadas e apura na hora a diferença, emitindo o **Talão Z de 80mm** com resumo por forma de pagamento (Dinheiro, M-Pesa, e-Mola, Cartão) e campos de assinatura para o operador e a gerência.

### 5. 🤝 Gestão de Clientes & Fornecedores Farmacêuticos
* **Fornecedores & Distribuidores:** Cadastro com NUIT, contacto, endereço e botão de ativação/desativação em 1 clique (`toggleStatus`). Associação direta de cada medicamento ao seu fornecedor habitual.
* **Clientes & Fiados (Contas Correntes):** Limite de crédito individual, extrato de compras fiadas e botão de **Impressão de Recibo Térmico** a cada amortização de dívida.

### 6. 🏛️ Conformidade Fiscal com IVA em Moçambique (CIVA & Modelo A)
* **Regime de Isenção (Artigo 9º do CIVA):** Medicamentos essenciais e fármacos da cesta de saúde pública faturam automaticamente a 0% de taxa.
* **Regime Normal (16%):** Dermocosméticos, produtos de higiene e artigos tributáveis calculam a taxa geral de 16%.
* **Mapa Fiscal de Apuramento de IVA (Modelo A):** Apuramento mensal automático entre IVA Liquidado nas vendas e IVA Dedutível em despesas documentadas, apurando o **Imposto a Pagar à AT (Campo 30)** ou **Crédito Fiscal a Reportar (Campo 31)** com exportação formal em PDF A4 pronto para a contabilidade.

### 7. 💉 Venda Integrada de Serviços de Saúde
A farmácia moderna é um centro de cuidados primários de saúde. No ZBIZ+, pode faturar no mesmo talão:
* **Medição de Pressão Arterial (Tensão);**
* **Administração de Medicamentos & Injeções;**
* **Curativos e Pensos Rápidos;**
* **Testes Rápidos de Diagnóstico (Malária, Glicemia, HIV);**
* *Vantagem:* Os serviços não movimentam o inventário físico e alimentam diretamente a receita líquida da farmácia.

### 8. 📋 Controlo Regulatório de Psicotrópicos (ANARME)
* Medicamentos com retenção obrigatória de receita médica solicitam automaticamente:
  * Nome do Médico Prescritor e Número da Cédula Profissional;
  * Número de Registo da Receita Médica;
  * Identificação do Paciente / Cuidador.
* Emissão de livro de registo e circulação de substâncias controladas pronto para apresentação aos inspetores da Saúde.

### 9. 💰 Pagamentos Nacionais de Moçambique
* Suporte nativo a pagamentos combinados:
  * 💵 **Numerário (Cash)** com atalhos de troco automático;
  * 📱 **M-Pesa** (Vodacom) com campo para código de transação;
  * 📲 **e-Mola** (Movitel);
  * 💳 **Cartão Bancário / POS** (Ponto 24, Millennium BIM, BCI, Standard Bank);
  * 🏦 **Transferência Bancária**.

### 10. 💾 Backups Sob Demanda & Segurança de Dados
* Geração de cópias de segurança completas (dumps SQL) em 1 clique no menu de definições.
* Download direto para o computador ou armazenamento em disco externo.
* Proteção de logótipo: visualização profissional da marca sobre fundo branco limpo em faturas, orçamentos e talões.

---

## 🔄 4. O FLUXO DIÁRIO NA FARMÁCIA (PASSO A PASSO)

```text
┌─────────────────────────────────────────────────────────────────────────┐
│ 1. INÍCIO DO TURNO: ABERTURA DE CAIXA                                   │
│    O operador autentica-se, insere o fundo de troco inicial (ex: 2.000 MT)│
│    e inicia o turno de vendas.                                          │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 2. ENTRADA DE MERCADORIA DO DISTRIBUIDOR                                │
│    Registo da fatura com seleção do Fornecedor, Lote, Validade,         │
│    Preço de Custo e Regime de IVA (Isento Art. 9º ou 16%).              │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 3. ATENDIMENTO AO CLIENTE NO BALCÃO (POS 2.0)                           │
│    • Pesquisa por nome comercial, Princípio Ativo (DCI) ou código.      │
│    • Seleção automática do lote com validade mais próxima (FEFO).       │
│    • Inclusão rápida de serviços de enfermagem (se aplicável).          │
│    • Escolha do cliente (Consumidor Final ou Cliente com NUIT/Crédito). │
│    • Pagamento via Dinheiro, M-Pesa, e-Mola ou Cartão.                  │
│    • Emissão do Talão Térmico de 80mm com logótipo da farmácia.         │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 4. MONITORIZAÇÃO PREVENTIVA AO LONGO DO DIA                             │
│    O diretor técnico consulta o Radar de Validades (alertas de 60 dias) │
│    e devolve ou faz promoção preventiva dos lotes antes de expirarem.   │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 5. FIM DO TURNO: FECHO CEGO DE CAIXA (TALÃO Z)                          │
│    O operador faz a contagem física do dinheiro na gaveta sem consultar │
│    o sistema. O software calcula quebra/sobra e emite o talão Z de fecho│
│    com assinaturas de conferência.                                      │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 6. FINAL DO MÊS: APURAMENTO FISCAL DE IVA (MODELO A)                    │
│    Extração em 1 clique do relatório de IVA em PDF para a AT e cópia    │
│    de segurança (Backup SQL) da base de dados.                          │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 📊 5. COMPARATIVO: ZBIZ+ PHARMACY vs MÉTODOS TRADICIONAIS

| Funcionalidade | Papel / Caderno | Excel / Planilhas | Software Genérico | ZBIZ+ Pharmacy |
| :--- | :---: | :---: | :---: | :---: |
| **Controlo de Lotes e Validades** | ❌ Impossível | ⚠️ Sujeito a erro | ⚠️ Manual | ✅ **Automático (FEFO + 3 Níveis)** |
| **Conformidade ANARME Moçambique** | ❌ Não | ❌ Não | ❌ Não | ✅ **Sim (Psicotrópicos & DCI)** |
| **Fecho Cego de Caixa Anti-Fraude** | ❌ Não | ❌ Não | ❌ Raro | ✅ **Sim (Talão Z com Auditoria)** |
| **Apuramento IVA (Art. 9º CIVA & AT)** | ❌ Manual | ⚠️ Muito complexo | ❌ Não adaptado | ✅ **Sim (Modelo A em PDF A4)** |
| **Vínculo Fornecedor-Artigo** | ❌ Não | ⚠️ Planilha separada | ⚠️ Básico | ✅ **Nativo com histórico** |
| **Integração M-Pesa & e-Mola** | ❌ Não | ❌ Não | ❌ Raro | ✅ **Nativo na finalização** |
| **Funciona sem Internet (Offline)** | ✅ Sim | ✅ Sim | ❌ A maioria bloqueia | ✅ **Sim, com licença local** |
| **Venda de Serviços Farmacêuticos** | ❌ Desorganiza stock | ❌ Manual | ❌ Não suportado | ✅ **Nativo sem travar inventário** |
| **Gestão de Fiados & Limite de Crédito** | ⚠️ Caderno solto | ⚠️ Manual | ⚠️ Simples | ✅ **Completo com Recibo Térmico** |
| **Backups Integrados em 1 Clique** | ❌ Zero | ⚠️ Manual | ⚠️ Requer TI | ✅ **Sim, direto no painel** |
| **Suporte Técnico Local em Moçambique** | ❌ Inexistente | ❌ Não | ❌ Internacional lento | ✅ **Rápido via WhatsApp/Telefone** |

---

## 🔑 6. LICENCIAMENTO TRANSPARENTE & IMPLANTAÇÃO SIMPLES

O ZBIZ+ disponibiliza duas modalidades simples de contratação:

1. **Modalidade Cloud (Nuvem):** Acesso de qualquer computador, tablet ou smartphone com backup automático diário e sincronização multi-filiais.
2. **Modalidade Local Offline (Instalação no Computador da Farmácia):** Ideal para zonas com falhas frequentes de energia ou internet. A farmácia recebe uma **Chave Serial de Software Oficial** (`ZBIZ-XXXX-XXXX-XXXX-XXXX`) e um **Certificado de Autenticidade em PDF**.

---

## 📞 7. CONTACTE-NOS PARA UMA DEMONSTRAÇÃO PRÁTICA

Estamos disponíveis para realizar uma demonstração do sistema diretamente no seu balcão ou via chamada remota, apresentando todas as telas e simulando vendas reais na sua farmácia.

* **WhatsApp / Telefone:** (+258) 86 213 4230
* **Email:** fdsmultiservices@gmail.com
* **Localização:** Quelimane / Moçambique

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Apoio Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email de Contacto:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Módulo Pharmacy 2026*  
────────────────────────────────────────────────────────────────────────────  
