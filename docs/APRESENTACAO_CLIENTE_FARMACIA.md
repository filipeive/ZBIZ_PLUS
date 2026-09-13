# ZBIZ+ PHARMACY: SISTEMA INTEGRADO DE GESTÃO FARMACÊUTICA & POS
## Apresentação Comercial & Manual de Operação para Farmácias e Drogarias
### `docs/APRESENTACAO_CLIENTE_FARMACIA.md`

---

## 🏥 1. O QUE É O ZBIZ+ PHARMACY?

O **ZBIZ+ Pharmacy** é um sistema completo e moderno de gestão farmacêutica, frente de caixa (POS) e controlo de inventário, desenvolvido especificamente para atender à realidade prática e às exigências legais de **Moçambique**.

Ele foi desenhado para proprietários de farmácias, diretores técnicos e operadores de caixa que precisam de **agilidade no balcão, segurança máxima contra quebras de caixa, controlo rigoroso de validades e conformidade total com a ANARME (Agência Nacional Reguladora de Medicamentos)**.

---

## 🎯 2. OS GRANDES PROBLEMAS QUE O ZBIZ+ RESOLVE NA SUA FARMÁCIA

| Desafio Comum na Farmácia | Como o ZBIZ+ Pharmacy Resolve |
| :--- | :--- |
| **Medicamentos vencidos na prateleira** | **Radar de Validades Automático (90, 60 e 30 dias)** e dispensa inteligente **FEFO** (*o lote que vence primeiro é vendido primeiro*). |
| **Filas longas e demora no atendimento** | **Frente de Caixa (POS 2.0) ultra-rápido**, pesquisa por nome comercial ou Princípio Ativo (DCI) e compatibilidade com leitores de código de barras. |
| **Diferenças de dinheiro e desvios de caixa** | **Controlo de turnos com abertura e fecho de caixa cego**, registo discriminado por Numerário, M-Pesa, e-Mola e Cartão POS. |
| **Medo de multas da fiscalização da ANARME** | **Rastreabilidade obrigatória por Lote e Validade**, com registo de médico e receita para medicamentos psicotrópicos. |
| **Internet que falha constantemente** | **Modo 100% Offline Garantido**, operando na rede local com ativação por Chave de Licença de Software Serial (`ZBIZ-XXXX-...`). |
| **Perda de dinheiro com vendas a fiado** | **Módulo de Dívidas Completo**: limite de crédito por cliente, extrato de conta corrente e recibo automático de amortização. |
| **Serviços de saúde não faturados** | **Cobrança integrada de serviços clínicos** (injeções, medição de pressão arterial, curativos, testes rápidos de glicemia/malária). |

---

## 💎 3. AS 8 PRINCIPAIS FUNCIONALIDADES DO SISTEMA

### 1. ⚡ Frente de Caixa (POS 2.0) Especializado para Balcão
* **Pesquisa Instantânea por DCI / Princípio Ativo:** Se o cliente pedir *Paracetamol*, o sistema lista imediatamente todas as marcas disponíveis na prateleira (Panadol, Empar, Paracetamol genérico) com preço e quantidade em tempo real.
* **Leitura Ótica por Código de Barras:** Atendimento ágil sem necessidade de digitação.
* **Descontos Flexíveis:** Suporte a promoções automáticas de balcão e aplicação de descontos autorizados (em valor fixo ou percentagem).
* **Impressão Térmica de Talões:** Emissão rápida de talões fiscais nos formatos padrão de 80mm ou 58mm.

### 2. 🛡️ Gestão de Lotes & Algoritmo FEFO (*First Expired, First Out*)
* Cada entrada de medicamento é associada ao seu **número de lote** e **data de validade**.
* Quando o operador adiciona um produto ao carrinho no POS, **o sistema seleciona automaticamente o lote com vencimento mais próximo**, impedindo que caixas antigas fiquem esquecidas no fundo da gaveta.

### 3. 🚨 Radar Preventivo de Validades em 3 Níveis
A tela inicial avisa a equipa com antecedência, evitando prejuízos:
* 🟡 **Alerta Amarelo (90 dias):** Planeie promoções preventivas ou rotação de stock.
* 🟠 **Alerta Laranja (60 dias):** Solicite a troca ou devolução junto do distribuidor/fornecedor.
* 🔴 **Alerta Vermelho / Bloqueio (30 dias ou Expirado):** O produto é sinalizado com urgência e bloqueado para evitar a venda de remédios vencidos ao público.

### 4. 💉 Venda Integrada de Serviços Farmacêuticos
A farmácia moderna é um centro de cuidados primários de saúde. No ZBIZ+, pode faturar no mesmo talão:
* **Medição de Pressão Arterial (Tensão);**
* **Administração de Medicamentos & Injeções;**
* **Curativos e Pensos Rápidos;**
* **Testes Rápidos de Diagnóstico (Malária, Glicemia, HIV);**
* *Vantagem:* Os serviços não bloqueiam o stock físico e entram diretamente no faturamento líquido da empresa.

### 5. 📋 Controlo Regulatório de Psicotrópicos (ANARME)
* Medicamentos com retenção obrigatória de receita médica solicitam automaticamente:
  * Nome do Médico Prescritor e Número da Cédula;
  * Número de Registo da Receita Médica;
  * Identificação do Paciente / Cuidador.
* Emissão do relatório de circulação de psicotrópicos pronto para apresentar aos inspectores da Saúde.

### 6. 💰 Fecho de Caixa Seguro & Meios de Pagamento de Moçambique
* Suporte nativo a pagamentos mistos:
  * 💵 **Numerário (Cash)** com cálculo de troco automático;
  * 📱 **M-Pesa** (Vodacom) com campo para referência da mensagem;
  * 📲 **e-Mola** (Movitel);
  * 💳 **Cartão Bancário / POS** (Ponto 24, BCI, Millennium BIM, Standard Bank);
  * 🏦 **Transferência Bancária**.
* **Livro-Razão Imutável:** Cada metical que entra ou sai fica registado com data, hora, operador e motivo, gerando o relatório diário de fecho de caixa sem margem para fraudes.

### 7. 🤝 Gestão de Clientes a Crédito (Fiados & Convénios)
* Cadastro de clientes de confiança, empresas parceiras ou famílias locais.
* Definição de **limite máximo de crédito**.
* Extrato individual com todas as compras a débito.
* Emissão de recibo a cada amortização parcial ou liquidação total da dívida.

### 8. 🏢 Multi-Filiais & Operação sem Internet (Offline-First)
* Se a sua farmácia abrir uma segunda ou terceira filial, o proprietário acompanha o faturamento de todas as lojas num único painel central.
* **Caiu a internet? A sua farmácia não pára!** O sistema opera em rede local com licença serial digital ativada, continuando a faturar normalmente sem depender dos cabos de fibra ou da rede móvel.

---

## 🔄 4. COMO FUNCIONA NO DIA-A-DIA DA SUA FARMÁCIA (PASSO A PASSO)

```text
┌─────────────────────────────────────────────────────────────────────────┐
│ 1. INÍCIO DO DIA: ABERTURA DE CAIXA                                     │
│    O funcionário entra com o seu utilizador e senha, insere o fundo     │
│    de maneio inicial (ex: 2.000 MT) e abre o turno.                     │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 2. RECEPÇÃO DE MERCADORIA DO DISTRIBUIDOR                               │
│    Ao receber a fatura do fornecedor, o responsável lança os produtos:  │
│    Nome, DCI, Preço de Custo, Preço de Venda, Lote e Validade.          │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 3. ATENDIMENTO AO CLIENTE NO BALCÃO (POS 2.0)                           │
│    • Cliente pede o medicamento: o operador lê o código de barras       │
│      ou pesquisa pelo princípio ativo.                                  │
│    • O sistema sugere o lote com expiração mais próxima (FEFO).         │
│    • Se o cliente também medir a pressão, adiciona-se o serviço.        │
│    • Escolha da forma de pagamento (Numerário / M-Pesa / Cartão).       │
│    • Impressão do talão térmico para o cliente.                         │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 4. MONITORAMENTO DE ALERTAS AO LONGO DO DIA                             │
│    O diretor técnico recebe notificações de lotes com menos de 60 dias  │
│    e toma decisões comerciais antes que ocorram perdas financeiras.     │
└────────────────────────────────────┬────────────────────────────────────┘
                                     │
┌────────────────────────────────────▼────────────────────────────────────┐
│ 5. FIM DO DIA: CONFERÊNCIA & FECHO DE CAIXA                             │
│    O operador realiza o fecho de turno. O sistema calcula o valor total │
│    esperado por tipo de pagamento (Numerário, M-Pesa, Cartão) e emite   │
│    o relatório Z consolidado para a gerência.                           │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 📊 5. COMPARATIVO: ZBIZ+ PHARMACY vs MÉTODOS TRADICIONAIS

| Funcionalidade | Papel / Caderno | Excel / Planilhas | Software Genérico | ZBIZ+ Pharmacy |
| :--- | :---: | :---: | :---: | :---: |
| **Controlo de Lotes e Validades** | ❌ Impossível | ⚠️ Sujeito a erro | ⚠️ Manual | ✅ **Automático (FEFO + 3 Níveis)** |
| **Conformidade ANARME Moçambique** | ❌ Não | ❌ Não | ❌ Não | ✅ **Sim (Psicotrópicos & DCI)** |
| **Integração com M-Pesa & e-Mola** | ❌ Não | ❌ Não | ❌ Raro | ✅ **Nativo no ecrã de venda** |
| **Funciona sem Internet (Offline)** | ✅ Sim | ✅ Sim | ❌ A maioria bloqueia | ✅ **Sim, com licença local** |
| **Venda de Serviços de Saúde** | ❌ Bagunça o stock | ❌ Manual | ❌ Não suportado | ✅ **Nativo sem travar inventário** |
| **Segurança contra Fraudes** | ❌ Zero | ❌ Nula | ⚠️ Média | ✅ **Auditoria completa de logs** |
| **Suporte Técnico Local em MZ** | ❌ Inexistente | ❌ Não | ❌ Internacional lento | ✅ **Rápido via WhatsApp/Telefone** |

---

## 🔑 6. LICENCIAMENTO TRANSPARENTE & IMPLANTAÇÃO SIMPLES

O ZBIZ+ disponibiliza duas modalidades simples de contratação:

1. **Modalidade Cloud (Nuvem):** Acesso de qualquer computador, tablet ou smartphone com backup automático diário.
2. **Modalidade Local Offline (Instalação no Computador da Farmácia):** Ideal para zonas com falhas frequentes de energia ou internet. A farmácia recebe uma **Chave Serial de Software Oficial** (`ZBIZ-XXXX-XXXX-XXXX-XXXX`) e um **Certificado de Autenticidade em PDF**.

---

## 📞 7. CONTACTE-NOS PARA UMA DEMONSTRAÇÃO PRÁTICA

Estamos disponíveis para realizar uma demonstração do sistema diretamente no seu balcão ou via chamada remota, apresentando todas as telas e simulando vendas reais na sua farmácia.

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: fdev-ms (FDS Multiservices)**  
*Engenharia de Software & Suporte Técnico:* `fdev-ms@fdevms:~/Filipe/reprosys$`  
*Contacto & Assistência Técnica:* (+258) 84 999 1122 · Quelimane / Moçambique  
*Email de Apoio:* `filipe.santos@fdsmultiservices.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Módulo Pharmacy 2026*  
────────────────────────────────────────────────────────────────────────────  
