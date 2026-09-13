# 📖 Manual do Usuário — ZBIZ+ Enterprise Cloud & POS Suite

**Versão 1.0.19 — Guia Completo para Gestores, Operadores de Caixa e Administradores**  
**Desenvolvido por:** Fdsmultiservices  
**Suporte Técnico Oficial:** (+258) 86 213 4230 | (+258) 84 724 0296 | fdsmultiservices@gmail.com  
**Servidor em Produção (Nuvem):** [http://146.235.224.99/zbiz_plus](http://146.235.224.99/zbiz_plus)  

---

## 📑 Índice Geral

1. [Apresentação do Sistema](#1-apresentação-do-sistema)
2. [Setores e Verticais Suportadas](#2-setores-e-verticais-suportadas)
3. [Primeiro Acesso, Autenticação e Segurança](#3-primeiro-acesso-autenticação-e-segurança)
4. [Licenciamento, Contador de Teste e Certificado Oficial](#4-licenciamento-contador-de-teste-e-certificado-oficial)
5. [Terminal de Venda / Frente de Caixa (POS)](#5-terminal-de-venda--frente-de-caixa-pos)
6. [Módulo Específico: Restaurante, Bar e Cafetaria](#6-módulo-específico-restaurante-bar-e-cafetaria)
7. [Módulo Específico: Farmácia e Cuidados de Saúde](#7-módulo-específico-farmácia-e-cuidados-de-saúde)
8. [Gestão de Produtos, Preços e Estoque](#8-gestão-de-produtos-preços-e-estoque)
9. [Gestão Financeira: Despesas, Dívidas e Fecho de Caixa](#9-gestão-financeira-despesas-dívidas-e-fecho-de-caixa)
10. [Relatórios Gerenciais e Fiscais](#10-relatórios-gerenciais-e-fiscais)
11. [Consola do Dono / Painel Multi-Empresa](#11-consola-do-dono--painel-multi-empresa)
12. [Atalhos de Teclado e Dicas de Produtividade](#12-atalhos-de-teclado-e-dicas-de-produtividade)
13. [Perguntas Frequentes (FAQ) e Suporte Técnico](#13-perguntas-frequentes-faq-e-suporte-técnico)

---

## 1. Apresentação do Sistema

O **ZBIZ+ Enterprise Suite** é uma plataforma SaaS e On-Premise integrada de gestão empresarial (ERP) e frente de caixa (POS) desenvolvida para a realidade económica de Moçambique.

### Principais Características:
* **Operação Híbrida**: Acesso via Nuvem com suporte a terminal de secretária local (Modo App Nativo e Kiosk).
* **Faturação & Moeda Nacional**: Cálculos em Meticais (MT / MZN), conformidade com regras de IVA e impressão térmica direta (ESC/POS 80mm e 58mm).
* **Meios de Pagamento Nacionais**: Suporte nativo a Dinheiro, Cartão (POS Bancário), M-Pesa e E-Mola.
* **Multi-Empresa & Multi-Filial**: Gestão centralizada para empresas com múltiplos balcões e lojas.

---

## 2. Setores e Verticais Suportadas

O ZBIZ+ adapta a interface e os recursos conforme o setor da sua empresa configurado no registo:

1. 🛒 **Retalho Geral & Supermercados (`retail`)**: Venda rápida com código de barras, controlo de unidades, packs e caixas.
2. 💊 **Farmácia & Saúde (`pharmacy`)**: Controlo rigoroso de lotes, datas de validade, retenção de receitas médicas e dosagens.
3. 🍽️ **Restaurante, Bar & Lounge (`restaurant`)**: Gestão de mesas com cores de ocupação, pedidos de balcão e divisão de contas.
4. 🔧 **Oficina Mecânica & Auto (`workshop`)**: Ordens de serviço (OS), peças aplicadas e mão de obra.
5. 👗 **Boutique & Vestuário (`clothing`)**: Gestão por tamanhos, cores e coleções.

---

## 3. Primeiro Acesso, Autenticação e Segurança

### 3.1 Pré-Registo de Nova Empresa
1. Aceda à página inicial e clique em **"Criar Conta Grátis"** ou aceda diretamente a `/register`.
2. Preencha os 3 passos guiados:
   - **Passo 1**: Nome da Empresa, NUIT, Província/Cidade, Endereço e Setor de Atividade.
   - **Passo 2**: Nome do Administrador, Telemóvel (84/85/86/87/82) e E-mail comercial.
   - **Passo 3**: Criação da Senha de Acesso.
3. **Aprovação de Segurança**: O pré-registo é analisado pela equipa da Fdsmultiservices. Assim que for aprovado, você recebe um **SMS no seu telemóvel** com as credenciais confirmadas e o link de acesso liberado.

### 3.2 Iniciar Sessão (Login)
1. Aceda a `/login`.
2. Insira o seu e-mail e palavra-passe.
3. Se a sua empresa estiver ativa ou em período de teste aprovado, você será direcionado para o Dashboard Geral da sua empresa.

### 3.3 Recuperação de Acesso
Caso se esqueça da senha, clique em **"Recuperar Palavra-passe"** ou solicite suporte imediato via WhatsApp da Fdsmultiservices: `(+258) 86 213 4230`.

---

## 4. Licenciamento, Contador de Teste e Certificado Oficial

### 4.1 Indicadores no Topo da Tela (Navbar)
O sistema apresenta de forma transparente o status do seu plano no cabeçalho superior:
* 🟢 **Teste Ativo (> 7 dias)**: Exibe badge verde `[● Teste: X dias]`.
* 🟡 **Alerta de Expiração (≤ 7 dias)**: Exibe badge âmbar `[● Teste: X dias]`.
* 🔴 **Reta Final (≤ 3 dias)**: Exibe badge vermelho pulsante alertando para a renovação.
* 🔴 **Licença Vencida**: Badge `[● Expirado]` acompanhado de banner informativo no topo.

### 4.2 Página de Perfil e Subscrição (`/profile`)
Ao clicar no badge ou no seu avatar no menu superior, você visualiza:
- Dias restantes com barra de progresso visual.
- Dados da empresa (Razão Social, NUIT, Ramo e Modalidade).
- Botão direto para **Activar Código de Licença**.
- Botão direto de WhatsApp para falar com a área comercial da Fdsmultiservices.

### 4.3 Ativação de Licença Definitiva (`ZBIZ-XXXX-XXXX-XXXX-XXXX`)
Quando subscreve ou renova um plano:
1. Aceda a **Activar Licença** (`/license/activate`).
2. Digite a Chave Serial de 16 caracteres recebida por SMS (ex: `ZBIZ-NJDK-M3RU-HPKH-VQQP`).
3. O sistema valida imediatamente a assinatura criptográfica e atualiza o seu plano para `active`.

### 4.4 Certificado Oficial em PDF
Os gestores podem descarregar o certificado timbrado em PDF de alta resolução com carimbo de autenticidade, assinatura e chave serial, útil para auditorias fiscais e comprovação de licenciamento do software.

---

## 5. Terminal de Venda / Frente de Caixa (POS)

Aceda ao POS através do botão destacado **"Terminal POS"** no topo da tela ou pelo atalho de secretária gerado pelo instalador Windows.

### 5.1 Realizar uma Venda Rápida
1. **Localizar Produto**:
   - Bipar o código de barras com o leitor USB.
   - Ou digitar o nome/código na barra de pesquisa rápida (`Ctrl+K`).
   - Ou clicar no cartão visual do produto na grelha de categorias.
2. **Ajustar Quantidades**: Utilize os botões `+` e `-` no carrinho lateral ou digite a quantidade diretamente.
3. **Finalizar Venda**:
   - Clique em **"Cobrar"** ou pressione a tecla `F4` / `Enter`.
   - Escolha o método de pagamento:
     - 💵 **Dinheiro**: Digite o valor entregue para cálculo automático do troco.
     - 📱 **M-Pesa / E-Mola**: Confirme a referência da transação móvel.
     - 💳 **POS / Cartão**: Confirme o comprovativo da máquina de cartão.
     - ⏳ **A Prazo (Conta Corrente / Dívida)**: Selecione o cliente cadastrado.
4. **Impressão de Recibo**: O recibo é impresso imediatamente na impressora térmica configurada.

---

## 6. Módulo Específico: Restaurante, Bar e Cafetaria

Se a sua empresa opera no ramo de alimentação e bebidas:

### 6.1 Painel Gráfico de Mesas
* O mapa do salão exibe as mesas organizadas por zonas (Salão Principal, Esplanada, Balcão/Bar).
* **Cores das Mesas**:
  - 🟢 **Verde (Livre)**: Mesa disponível para novos clientes.
  - 🔴 **Vermelho (Ocupada)**: Mesa com conta aberta e pedidos em consumo.
  - 🟡 **Amarelo (Em Pagamento)**: Conta solicitada aguardando encerramento.

### 6.2 Lançar Pedidos na Mesa
1. Clique sobre a mesa livre (ex: **Mesa 03**).
2. Adicione os itens solicitados (Bebidas, Pratos, Entradas).
3. Clique em **"Confirmar Pedido"** (os pedidos podem ser direcionados para o ecrã da cozinha/KDS).
4. A mesa passa automaticamente para o status **Ocupada**.

### 6.3 Transferir Mesa ou Juntar Contas
Caso o cliente mude de lugar, utilize a opção **"Transferir Mesa"** e selecione o novo número. Todos os itens em aberto são transferidos automaticamente.

### 6.4 Fecho de Mesa e Divisão de Conta
1. Abra a mesa ocupada e clique em **"Emitir Pré-Conta"** para conferência do cliente.
2. Ao receber o pagamento, clique em **"Fechar Conta"**, selecione os meios de pagamento (permite dividir: parte em M-Pesa e parte em Dinheiro) e imprima a fatura final.

---

## 7. Módulo Específico: Farmácia e Cuidados de Saúde

Para estabelecimentos farmacêuticos licenciados:

### 7.1 Lotes e Datas de Validade (FEFO)
* O sistema aplica a regra **FEFO** (*First Expired, First Out*): os lotes que vencem primeiro são sugeridos prioritariamente no caixa.
* O cadastro exige: Número do Lote, Laboratório Fabricante e Data de Validade.

### 7.2 Medicamentos Controlados e Prescrições
* Ao vender itens de receita médica obrigatória, o POS solicita:
  - Nome do Médico Prescritor e Número da Ordem dos Médicos.
  - Nome e Identificação do Paciente.
  - Registo em relatório de substâncias sujeitas a controlo sanitário.

---

## 8. Gestão de Produtos, Preços e Estoque

### 8.1 Cadastrar Novo Produto
1. Aceda a **Produtos** → **Novo Produto**.
2. Preencha: Nome, Código de Barras (EAN), Categoria, Preço de Custo e Preço de Venda.
3. Defina o **Estoque Mínimo de Alerta** (o sistema avisa quando o estoque estiver próximo de esgotar).
4. Guarde o produto.

### 8.2 Entradas e Ajustes de Estoque
* **Entrada por Compra**: Registe a chegada de mercadoria com o fornecedor e valor de custo para atualização automática do custo médio.
* **Ajuste de Inventário**: Para regularizar quebras, avarias ou contagens físicas periódicas.

---

## 9. Gestão Financeira: Despesas, Dívidas e Fecho de Caixa

### 9.1 Abertura e Fecho de Caixa (Turno de Balcão)
* **Abertura**: O operador inicia o turno informando o fundo de maneio inicial (troco em caixa).
* **Fecho Cego**: No fim do expediente, o operador conta e declara os valores físicos sem ver o total do sistema. O gestor pode auditar eventuais quebras ou sobras de caixa.

### 9.2 Controlo de Devedores (Vendas a Crédito)
* Registe clientes autorizados a comprar a prazo.
* Emita extratos de conta corrente detalhados.
* Ao receber amortizações, lance o valor abatendo o saldo devedor com emissão do recibo de quitação.

### 9.3 Controlo de Despesas Operacionais
Lance despesas do dia-a-dia (energia, água, internet, salários, materiais) categorizadas para apuração precisa do Lucro Líquido no final do mês.

---

## 10. Relatórios Gerenciais e Fiscais

Aceda ao menu **Relatórios** para consultar:
* 📈 **Demonstração do Fluxo de Caixa**: Entradas vs Saídas diárias, semanais e mensais.
* 🏆 **Curva ABC de Produtos**: Descubra os produtos mais rentáveis e os que têm menor giro.
* 👥 **Desempenho por Operador**: Vendas realizadas, tickets médios e descontos concedidos por cada funcionário.
* 📄 **Exportação**: Todos os relatórios podem ser exportados com um clique para **Excel (.xlsx)** ou **PDF**.

---

## 11. Consola do Dono / Painel Multi-Empresa

Para administradores que gerenciam múltiplas filiais ou clientes:
* **Visão Consolidada**: Acesso a `/owner/tenants` com monitorização de status de cada filial.
* **Aprovação de Testes**: Definição flexível do período experimental (7, 14, 30 ou 60 dias) e envio automático de SMS com credenciais.
* **Acesso como Suporte (Impersonate)**: O administrador pode entrar com um clique no painel da empresa cliente para prestar suporte técnico direto sem precisar saber a senha pessoal do utilizador.
* **Emissão e Revogação de Licenças**: Geração imediata de chaves seriais e emissão de certificados oficiais em PDF.

---

## 12. Atalhos de Teclado e Dicas de Produtividade

| Tecla de Atalho | Ação Executada |
| :--- | :--- |
| `Ctrl + K` | Abrir busca rápida global de produtos e comandos |
| `F4` ou `Enter` | Ir para a tela de pagamento no Terminal POS |
| `Esc` | Cancelar modal, fechar pop-up ou limpar busca |
| `F8` | Abrir gaveta de dinheiro (se configurada) |
| `Ctrl + P` | Imprimir documento / relatório atual |
| `Tab` | Avançar para o próximo campo de formulário |

---

## 13. Perguntas Frequentes (FAQ) e Suporte Técnico

### P: O que acontece quando o período de teste ou a licença expira?
**R:** O sistema entra automaticamente em **Modo Somente-Leitura**. Nenhum dado é apagado e continuará a conseguir consultar todo o seu histórico e relatórios. Apenas as operações de venda e gravação de novos registos ficam bloqueadas até a ativação da nova licença.

### P: Como posso instalar o ZBIZ+ nos computadores de caixa da minha loja?
**R:** Consulte o guia anexo [INSTRUCOES_DE_INSTALACAO_E_USO.md](file:///home/fdev-ms/Filipe/ZBIZ_PLUS/INSTRUCOES_DE_INSTALACAO_E_USO.md). No Windows, basta rodar o arquivo `install-windows.bat` e escolher a Opção `1` para ter o atalho de secretária profissional em segundos.

### P: O sistema emite faturas em conformidade com o IVA de Moçambique?
**R:** Sim, o ZBIZ+ permite configurar as taxas vigentes de IVA (16%), isenções e retenções na fonte, calculando os impostos automaticamente em cada fatura.

---

### 📞 Contactos de Apoio e Assistência Técnica

A equipa de engenharia e suporte da **Fdsmultiservices** está pronta para atendê-lo:

* 📱 **WhatsApp / Linha Direta:** `(+258) 86 213 4230`
* 📱 **Linha Alternativa:** `(+258) 84 724 0296`
* ✉️ **E-mail:** `fdsmultiservices@gmail.com`
* 🌐 **Portal ZBIZ+ Nuvem:** [http://146.235.224.99/zbiz_plus](http://146.235.224.99/zbiz_plus)
