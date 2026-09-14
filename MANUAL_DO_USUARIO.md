# 📖 Manual do Usuário — ZBIZ+ Enterprise Cloud & POS Suite

**Versão 1.0.20 — Guia Completo para Gestores, Operadores de Caixa e Administradores**  
**Desenvolvido por:** Fdsmultiservices  
**Suporte Técnico Oficial:** (+258) 86 213 4230 | (+258) 84 724 0296 | fdsmultiservices@gmail.com  
**Servidor em Produção (Nuvem):** [http://146.235.224.99/zbiz_plus](http://146.235.224.99/zbiz_plus)  

---

## 📑 Índice Geral

1. [Apresentação do Sistema](#1-apresentação-do-sistema)
2. [Setores e Verticais Suportadas](#2-setores-e-verticais-suportadas)
3. [Primeiro Acesso, Autenticação e Segurança](#3-primeiro-acesso-autenticação-e-segurança)
4. [Licenciamento, Contador de Teste e Certificado Oficial](#4-licenciamento-contador-de-teste-e-certificado-oficial)
5. [Terminal de Venda / Frente de Caixa (POS 2.0)](#5-terminal-de-venda--frente-de-caixa-pos-20)
   - 5.1 Realizar uma Venda Rápida
   - 5.2 Associação e Cadastro Rápido de Clientes no Balcão
   - 5.3 Alternância Fiscal de IVA (Incluso, +16%, Isento Art. 9º CIVA)
   - 5.4 Comprovativo Térmico com Logótipo Oficial da Empresa
6. [Gestão de Clientes e Contas Correntes](#6-gestão-de-clientes-e-contas-correntes)
7. [Gestão de Fornecedores e Distribuidores](#7-gestão-de-fornecedores-e-distribuidores)
8. [Módulo Específico: Farmácia e Cuidados de Saúde (ANARME)](#8-módulo-específico-farmácia-e-cuidados-de-saúde-anarme)
9. [Módulo Específico: Restaurante, Bar e Cafetaria](#9-módulo-específico-restaurante-bar-e-cafetaria)
10. [Gestão de Produtos, Preços e Estoque](#10-gestão-de-produtos-preços-e-estoque)
11. [Gestão de Turnos e Fecho Cego de Caixa (Anti-Fraude)](#11-gestão-de-turnos-e-fecho-cego-de-caixa-anti-fraude)
12. [Gestão Financeira: Dívidas, Recibos Térmicos e Despesas](#12-gestão-financeira-dívidas-recibos-térmicos-e-despesas)
13. [Fiscalidade: Mapa de Apuramento de IVA (Modelo A - AT Moçambique)](#13-fiscalidade-mapa-de-apuramento-de-iva-modelo-a---at-moçambique)
14. [Definições da Empresa, Branding e Backups do Sistema](#14-definições-da-empresa-branding-e-backups-do-sistema)
15. [Consola do Dono / Painel Multi-Empresa](#15-consola-do-dono--painel-multi-empresa)
16. [Atalhos de Teclado e Dicas de Produtividade](#16-atalhos-de-teclado-e-dicas-de-produtividade)
17. [Perguntas Frequentes (FAQ) e Suporte Técnico](#17-perguntas-frequentes-faq-e-suporte-técnico)

---

## 1. Apresentação do Sistema

O **ZBIZ+ Enterprise Suite** é uma plataforma SaaS e On-Premise integrada de gestão empresarial (ERP) e frente de caixa (POS) concebida para a realidade económica e fiscal de Moçambique.

### Principais Características:
* **Operação Híbrida**: Acesso via Nuvem com suporte a terminal de secretária local (Modo App Nativo e Kiosk) e operação offline resiliente.
* **Faturação & Moeda Nacional**: Cálculos rigorosos em Meticais (MT / MZN), conformidade com regras fiscais de IVA de Moçambique (CIVA) e impressão térmica ESC/POS direta (80mm e 58mm).
* **Meios de Pagamento Nacionais**: Suporte nativo a Dinheiro, Cartão (POS Bancário físico), M-Pesa, E-Mola e Vendas a Crédito.
* **Multi-Empresa & Multi-Filial**: Gestão centralizada para empresas com múltiplos balcões, armazéns e lojas.
* **Identidade Visual Dinâmica**: Emissão de documentos timbrados, cotações, faturas e talões térmicos com o logótipo oficial do seu negócio.

---

## 2. Setores e Verticais Suportadas

O ZBIZ+ adapta a interface, formulários e regras conforme o setor configurado na sua empresa:

1. 💊 **Farmácia & Saúde (`pharmacy`)**: Rastreabilidade por Lote e Validade, alertas de expiração a 90/60/30 dias, algoritmo FEFO (*First Expire, First Out*), isenção de IVA (Art. 9º CIVA) e controlo de psicotrópicos (ANARME).
2. 🛒 **Retalho Geral & Supermercados (`retail`)**: Venda ultrarrápida com código de barras, leitura contínua, controlo de unidades, packs e caixas.
3. 🍽️ **Restaurante, Bar & Lounge (`restaurant`)**: Painel de mesas com cores de ocupação em tempo real, pedidos de balcão e divisão de contas.
4. 🔧 **Oficina Mecânica & Auto (`workshop`)**: Ordens de serviço (OS), peças aplicadas, mão de obra e estado do veículo.
5. 🖨️ **Reprografia, Gráfica & Brindes (`services`)**: Ordem de produção, insumos vinculados, sinal/adiantamento e cotações formais.
6. 👗 **Boutique & Vestuário (`clothing`)**: Grade por tamanhos, cores e coleções.

---

## 3. Primeiro Acesso, Autenticação e Segurança

### 3.1 Pré-Registo de Nova Empresa
1. Aceda à página inicial (`/`) e clique em **"Criar Conta Grátis"** ou aceda diretamente a `/register`.
2. Preencha os 3 passos guiados:
   - **Passo 1**: Nome Comercial da Empresa, NUIT, Província/Cidade, Endereço e Setor de Atividade.
   - **Passo 2**: Nome do Administrador, Telemóvel (84/85/86/87/82) e E-mail corporativo.
   - **Passo 3**: Criação da Palavra-passe de Acesso.
3. **Aprovação de Segurança**: O pré-registo é analisado pela equipa da Fdsmultiservices. Assim que aprovado, você recebe um **SMS no seu telemóvel** com as credenciais confirmadas e o link de acesso imediato.

### 3.2 Iniciar Sessão (Login)
1. Aceda a `/login`.
2. Insira o seu e-mail e palavra-passe.
3. Se a sua empresa estiver ativa ou em período de teste aprovado, você será direcionado para o Dashboard Geral da sua filial.

### 3.3 Recuperação de Acesso
Caso se esqueça da senha, utilize o link **"Recuperar Palavra-passe"** ou solicite assistência direta via WhatsApp da Fdsmultiservices: `(+258) 86 213 4230`.

---

## 4. Licenciamento, Contador de Teste e Certificado Oficial

### 4.1 Indicadores no Topo da Tela (Navbar)
O sistema apresenta de forma transparente o estado da subscrição no cabeçalho superior:
* 🟢 **Teste Ativo (> 7 dias)**: Exibe badge verde `[● Teste: X dias]`.
* 🟡 **Alerta de Expiração (≤ 7 dias)**: Exibe badge âmbar `[● Teste: X dias]`.
* 🔴 **Reta Final (≤ 3 dias)**: Exibe badge vermelho pulsante alertando para a renovação.
* 🔴 **Licença Vencida**: Badge `[● Expirado]` com transição para modo de consulta somente-leitura.

### 4.2 Ativação de Licença Definitiva (`ZBIZ-XXXX-XXXX-XXXX-XXXX`)
Quando subscrever ou renovar um plano:
1. Aceda a **Activar Licença** (`/license/activate`).
2. Digite a Chave Serial de 16 caracteres recebida por SMS (ex: `ZBIZ-4F92-K81M-Q7P3-9A2E`).
3. O sistema valida imediatamente a chave serial e atualiza a sua empresa para `active`.

### 4.3 Certificado Oficial em PDF
Os gestores podem descarregar o certificado timbrado em PDF de alta resolução com selo de autenticidade, assinatura e chave serial, útil para auditorias fiscais e comprovação de licenciamento do software.

---

## 5. Terminal de Venda / Frente de Caixa (POS 2.0)

Aceda ao POS através do botão destacado **"Terminal POS"** no topo da tela ou pelo atalho de secretária gerado pelo instalador Windows/Linux.

### 5.1 Realizar uma Venda Rápida
1. **Localizar Produto**:
   - Bipar o código de barras com leitor USB (foco automático permanente).
   - Ou digitar o nome/código na barra de pesquisa rápida (`Ctrl+K`).
   - Ou clicar no cartão visual do produto na grelha de categorias.
2. **Ajustar Quantidades**: Utilize os botões `+` e `-` no carrinho ou digite a quantidade diretamente.
3. **Finalizar Venda**:
   - Clique em **"Cobrar"** ou pressione a tecla `F4` / `Enter`.
   - Escolha o método de pagamento:
     - 💵 **Dinheiro**: Digite o valor entregue para cálculo automático e instantâneo do troco.
     - 📱 **M-Pesa / E-Mola**: Confirme a referência da transação móvel.
     - 💳 **POS / Cartão**: Registe o comprovativo da máquina de cartão.
     - ⏳ **A Prazo (Conta Corrente / Dívida)**: Selecione o cliente cadastrado.

### 5.2 Associação e Cadastro Rápido de Clientes no Balcão
No topo do carrinho de compras do POS:
* **Seleção Rápida**: Digite o nome ou telefone do cliente no seletor suspenso.
* **Cliente Anónimo (Balcão)**: Opção padrão para clientes ocasionais sem identificação.
* **Botão "+ Novo"**: Permite abrir um formulário modal no próprio caixa sem sair da tela de venda. Registe Nome, NUIT e Telefone em 10 segundos para emitir a fatura com NUIT imediatamente.

### 5.3 Alternância Fiscal de IVA (Incluso, +16%, Isento Art. 9º CIVA)
Na barra de totalização do POS, existe o seletor rápido de IVA:
* **IVA Incluso (Padrão de Varejo)**: O preço de prateleira já contém o imposto; o sistema extrai e discrimina a base tributável e a parcela de IVA no talão.
* **+16% (IVA Acrescido)**: Adiciona 16% sobre o subtotal no momento da faturação formal B2B.
* **Isento (Artigo 9º CIVA)**: Aplica isenção legal com a devida anotação no comprovativo (*"Isento nos termos do Artigo 9º do CIVA"*), obrigatório para medicamentos de farmácia e produtos de primeira necessidade.

### 5.4 Comprovativo Térmico com Logótipo Oficial da Empresa
Ao concluir a venda, o comprovativo térmico (80mm ou 58mm) é emitido com:
* Logótipo da empresa em alta definição (se carregado nas Definições).
* Cabeçalho fiscal: Razão Social, NUIT, Endereço, Contato.
* Itens com quantidade, preço unitário e valor total.
* Discriminação do IVA (Taxa, Base Tributável, Montante Liquidado ou Artigo de Isenção).
* Resumo de pagamento: Método utilizado, Valor Entregue e Troco apurado.
* Identificação do Operador e Número do Turno ativo.

---

## 6. Gestão de Clientes e Contas Correntes

Aceda ao menu **Clientes** (`/customers`):
* **Ficha Cadastral Completa**: Nome, Nome Comercial, NUIT, Telefone, E-mail, Endereço Físico e Limite de Crédito aprovado.
* **Histórico de Compras**: Consulte todas as faturas e recibos emitidos para o cliente com status de quitação.
* **Saldo Devedor em Aberto**: Painel visual indicando o valor exato que o cliente deve à empresa.
* **Extrato de Conta**: Visualização cronológica de compras a prazo e pagamentos efetuados.

---

## 7. Gestão de Fornecedores e Distribuidores

Aceda ao menu **Fornecedores** (`/suppliers`):
* **Registo de Fornecedores e Laboratórios**: Nome da Empresa, Contato Principal, NUIT, Telefone, E-mail e Endereço.
* **Condições Comerciais**: Prazo acordado de pagamento (ex: Pronto Pagamento, 15 dias, 30 dias, 60 dias).
* **Estado de Ativação**: Habilite ou desabilite fornecedores com um clique. Fornecedores inativos são ocultados nos novos lançamentos sem perder o histórico contábil.
* **Vínculo Direto nos Produtos**: Ao criar ou editar qualquer produto, selecione o Fornecedor de referência.
* **Relatório de Compras**: Consulte rapidamente quais produtos e lotes foram fornecidos por cada distribuidor.

---

## 8. Módulo Específico: Farmácia e Cuidados de Saúde (ANARME)

Para farmácias, drogarias e postos de medicamentos:

### 8.1 Gestão de Lotes e Datas de Validade (FEFO)
* O sistema aplica a regra **FEFO** (*First Expired, First Out*): o caixa prioriza sempre o lote com data de expiração mais próxima.
* **Monitor de Validades**:
  - 🔴 **Vencidos**: Bloqueio imediato para venda no POS, prevenindo sanções da ANARME.
  - 🟠 **Alerta Crítico (< 30 dias)**: Destaque visual e aviso sonoro para queima rápida de estoque.
  - 🟡 **Atenção (30 a 90 dias)**: Notificação gerencial para planejamento de promoções ou devolução ao distribuidor.

### 8.2 Receitas Médicas e Psicotrópicos
* No balcão, a venda de medicamentos de receita médica obrigatória registra:
  - Médico Prescritor e Número da Ordem dos Médicos.
  - Nome, NUIT e BI do Paciente.
  - Livro de Registo de Psicotrópicos e Estupefacientes exportável para a Direcção Provincial de Saúde.

---

## 9. Módulo Específico: Restaurante, Bar e Cafetaria

Se a sua empresa opera no ramo de alimentação e bebidas:

### 9.1 Painel Gráfico de Mesas
* O mapa do salão exibe as mesas organizadas por zonas (Salão Principal, Esplanada, Balcão/Bar).
* **Cores das Mesas**:
  - 🟢 **Verde (Livre)**: Mesa disponível para novos clientes.
  - 🔴 **Vermelho (Ocupada)**: Mesa com conta aberta e pedidos em consumo.
  - 🟡 **Amarelo (Em Pagamento)**: Conta solicitada aguardando encerramento.

### 9.2 Lançar Pedidos na Mesa
1. Clique sobre a mesa livre (ex: **Mesa 03**).
2. Adicione os itens solicitados (Bebidas, Pratos, Entradas).
3. Clique em **"Confirmar Pedido"** (os pedidos podem ser direcionados para o ecrã da cozinha/KDS).
4. A mesa passa automaticamente para o status **Ocupada**.

### 9.3 Transferir Mesa ou Juntar Contas
Caso o cliente mude de lugar, utilize a opção **"Transferir Mesa"** e selecione o novo número. Todos os itens em aberto são transferidos automaticamente.

### 9.4 Fecho de Mesa e Divisão de Conta
1. Abra a mesa ocupada e clique em **"Emitir Pré-Conta"** para conferência do cliente.
2. Ao receber o pagamento, clique em **"Fechar Conta"**, selecione os meios de pagamento (permite dividir: parte em M-Pesa e parte em Dinheiro) e imprima a fatura final.

---

## 10. Gestão de Produtos, Preços e Estoque

### 10.1 Cadastrar Novo Produto
1. Aceda a **Produtos** → **Novo Produto**.
2. Preencha:
   - Nome Comercial do Produto.
   - Código de Barras (EAN-13) ou clique em "Gerar Código".
   - Categoria e Fornecedor de Referência.
   - Preço de Custo e Preço de Venda.
   - Taxa de IVA aplicável (16% ou Isento Art. 9º).
3. Defina o **Estoque Mínimo de Alerta** (o sistema emite aviso de reposição quando o estoque atingir esse nível).
4. Guarde o produto.

### 10.2 Entradas e Ajustes de Estoque
* **Entrada por Compra**: Registe a chegada de mercadoria com o fornecedor e valor de custo para atualização automática do custo médio ponderado.
* **Ajuste de Inventário**: Regularização de quebras, perdas, avarias ou contagens físicas periódicas com justificativa auditada.

---

## 11. Gestão de Turnos e Fecho Cego de Caixa (Anti-Fraude)

O ZBIZ+ implementa as melhores práticas de auditoria contábil com o mecanismo de **Fecho Cego de Caixa**:

### 11.1 Abertura de Turno
1. Ao iniciar o dia ou troca de funcionário, aceda a **Caixas & Turnos** (`/cash-shifts`) ou ao POS.
2. O sistema solicita o **Fundo de Maneio Inicial** (troco deixado na gaveta).
3. O turno é aberto com data, hora e operador identificado.

### 11.2 Operação Contínua
Todas as vendas realizadas pelo operador são vinculadas automaticamente ao seu turno ativo (`cash_shift_id`). O sistema contabiliza em tempo real as entradas em Dinheiro, M-Pesa, E-Mola, Cartão e Fiados.

### 11.3 Fecho Cego Anti-Fraude
No encerramento do expediente:
1. O operador clica em **"Encerrar Turno"**.
2. **Regra de Ouro**: A tela NÃO revela ao operador quanto dinheiro deveria existir na gaveta. Isso impede desvios ou retiradas oportunistas de excedentes.
3. O operador realiza a contagem física utilizando a **Calculadora de Meticais (MZN)**:
   - Digita a quantidade de notas de 1000 MT, 500 MT, 200 MT, 100 MT, 50 MT, 20 MT e moedas.
   - O sistema calcula automaticamente o total declarado.
4. O operador confirma o fecho.

### 11.4 Apuramento de Quebra / Sobra & Talão Fecho Z
* Assim que o operador submete a contagem, o gerente tem acesso ao confronto:
  - **Saldo Esperado pelo Sistema** vs **Saldo Físico Declarado**.
  - 🟢 **Bateu Certo**: Diferença 0,00 MT.
  - 🔴 **Quebra de Caixa (Falta)**: Diferença negativa exigindo justificativa.
  - 🟡 **Sobra de Caixa (Excesso)**: Diferença positiva para auditoria.
* **Impressão do Fecho Z**: Emissão do talão térmico resumido com resumo financeiro por meio de pagamento, quebra/sobra e campos de assinatura do Operador e do Gerente.

---

## 12. Gestão Financeira: Dívidas, Recibos Térmicos e Despesas

### 12.1 Controlo de Devedores (Vendas a Crédito)
* Visualize todas as vendas em aberto organizadas por cliente.
* Acompanhe os prazos de vencimento e valores acumulados.

### 12.2 Amortização de Dívidas, Recibo Oficial em Modelo A4 e Extrato Geral
1. Aceda a **Gestão de Dívidas** (`/debts`) e selecione a conta do cliente.
2. Clique em **"Registar Pagamento / Amortização"**.
3. Informe o valor amortizado e a forma de pagamento (Dinheiro, M-Pesa, E-Mola, Cartão POS, Transferência Bancária).
4. O saldo devedor é abatido imediatamente em tempo real.
5. **Emissão de Comprovativos (Modelo A4 Oficial & Térmico)**:
   - 📄 **Recibo Oficial em Modelo A4**: Documento formal timbrado com o logótipo da empresa, NUIT, dados completos do devedor, demonstrativo financeiro detalhado (valor original, prestações anteriores, valor pago, saldo devedor restante), termos legais de quitação ou amortização parcial e assinaturas com carimbo.
   - ⬇️ **Descarregar em PDF (A4)**: Exportação com 1 clique de arquivo PDF timbrado de alta resolução gerado pelo motor DomPDF.
   - 🧾 **Talão Térmico (80mm / 58mm)**: Opção rápida para impressão direta em impressoras térmicas de balcão ESC/POS.
   - 📋 **Extrato Geral da Dívida em A4**: Na barra superior da dívida, clique em **"Extrato A4"** para emitir uma certidão completa com o histórico cronológico de todas as parcelas amortizadas e o saldo consolidado atual.

### 12.3 Controlo de Despesas Operacionais
Lance despesas do dia-a-dia (energia, água, internet, salários, materiais) devidamente categorizadas para apuração precisa do Lucro Líquido Real no final do mês.

---

## 13. Fiscalidade: Mapa de Apuramento de IVA (Modelo A - AT Moçambique)

Em conformidade com as exigências da Autoridade Tributária de Moçambique:

### 13.1 Consulta do Mapa de IVA
1. Aceda a **Relatórios** → **Apuramento de IVA (CIVA)** (`/reports/tax-iva`).
2. Selecione o período de apuramento (mês específico ou intervalo customizado).
3. O sistema calcula automaticamente:
   - **Vendas Isentas (Artigo 9º do CIVA)**: Medicamentos e bens essenciais isentos.
   - **Vendas Tributáveis à Taxa Normal (16%)**: Total faturado com incidência de IVA.
   - **Base Tributável Líquida**: Valor sobre o qual incide o imposto.
   - **IVA Liquidado**: Valor total do imposto cobrado aos clientes.
   - **Imposto Total a Entregar ao Estado**: Saldo fiscal a recolher no Modelo A da AT.

### 13.2 Exportação do Modelo A em PDF Oficial
Clique em **"Exportar Modelo A (PDF)"**:
* O sistema gera um documento timbrado com o logótipo da sua empresa, NUIT, Razão Social, enquadramento fiscal e quadro demonstrativo idêntico ao modelo oficial da AT para entrega ao seu contabilista certificado.

---

## 14. Definições da Empresa, Branding e Backups do Sistema

Aceda a **Definições do Sistema** (`/settings`):

### 14.1 Personalização e Branding (Logótipo)
* Faça o upload do logótipo oficial da sua empresa (PNG, JPG, WebP).
* **Fundo Neutro Limpo**: O visualizador apresenta o logótipo sobre fundo branco puro com contorno suave (`bg-white border-2 border-slate-200`), garantindo que marcas com fundos claros ou transparentes fiquem perfeitas.
* O logótipo é propagado instantaneamente para:
  - Faturas Oficiais A4 em PDF (`/documents/templates/invoice_pdf`).
  - Cotações Comerciais em PDF (`/documents/templates/quotation_pdf`).
  - Recibos e Extratos Oficiais de Dívida em Modelo A4 (`/debts/payments/{id}/receipt`).
  - Recibos Térmicos de Venda do POS (80mm e 58mm).
  - Recibos Térmicos de Amortização de Dívidas.
  - Relatórios Oficiais de Apuramento de IVA Modelo A.

### 14.2 Gestão de Backups & Cópias de Segurança
Na aba **"6. Backups & Base de Dados"**:
* **Criar Backup Agora**: Gera com 1 clique um dump SQL integral e criptograficamente íntegro de toda a base de dados do tenant.
* **Tabela de Histórico**: Visualize o nome do ficheiro (com carimbo de data e hora), tamanho em KB/MB e data de criação.
* **Descarregar Backup**: Descarregue o ficheiro `.sql` para o seu computador ou pen-drive externa para segurança física dos dados.
* **Eliminar Backup**: Exclua arquivos antigos quando necessário para manter o armazenamento limpo.

---

## 15. Consola do Dono / Painel Multi-Empresa

Para administradores que gerenciam múltiplas filiais ou clientes SaaS:
* **Visão Consolidada**: Acesso a `/owner/tenants` com monitorização do status operacional de cada filial.
* **Aprovação de Testes**: Definição flexível do período experimental (7, 14, 30 ou 60 dias) e envio automático de SMS com credenciais.
* **Acesso como Suporte (Impersonate)**: O administrador pode entrar com um clique no painel da empresa cliente para prestar suporte técnico direto sem precisar saber a senha pessoal do utilizador.
* **Emissão e Revogação de Licenças**: Geração imediata de chaves seriais e emissão de certificados oficiais em PDF.

---

## 16. Atalhos de Teclado e Dicas de Produtividade

| Tecla de Atalho | Ação Executada |
| :--- | :--- |
| `Ctrl + K` | Abrir busca rápida global de produtos e comandos |
| `F4` ou `Enter` | Ir para a tela de pagamento no Terminal POS |
| `Esc` | Cancelar modal, fechar pop-up ou limpar busca |
| `F8` | Abrir gaveta de dinheiro (se configurada) |
| `Ctrl + P` | Imprimir documento / relatório atual |
| `Tab` | Avançar para o próximo campo de formulário |

---

## 17. Perguntas Frequentes (FAQ) e Suporte Técnico

### P: O que acontece quando o período de teste ou a licença expira?
**R:** O sistema entra automaticamente em **Modo Somente-Leitura**. Nenhum dado é apagado e continuará a conseguir consultar todo o seu histórico e relatórios. Apenas as operações de venda e gravação de novos registos ficam bloqueadas até a ativação da nova licença.

### P: Como posso instalar o ZBIZ+ nos computadores de caixa da minha loja?
**R:** Consulte o guia anexo [INSTRUCOES_DE_INSTALACAO_E_USO.md](file:///home/fdev-ms/Filipe/ZBIZ_PLUS/INSTRUCOES_DE_INSTALACAO_E_USO.md). No Windows, basta rodar o arquivo `install-windows.bat` e escolher a Opção `1` para ter o atalho de secretária profissional em segundos.

### P: Os medicamentos devem pagar 16% de IVA?
**R:** Não. Conforme o Artigo 9º do Código do IVA de Moçambique, os produtos farmacêuticos, medicamentos e artigos essenciais de saúde gozam de isenção de IVA. No ZBIZ+, basta selecionar a opção "Isento (Art. 9º CIVA)" para que o talão saia legalmente correto e o Mapa de IVA Modelo A registre a isenção de forma automática.

### P: Como garanto a segurança dos dados da minha empresa?
**R:** Aceda a **Definições** → **Backups & Base de Dados** e clique em **"Criar Backup Agora"** regularmente. Guarde uma cópia do ficheiro `.sql` descarregado num disco externo ou pendrive segura.

---

### 📞 Contactos de Apoio e Assistência Técnica

A equipa de engenharia e suporte da **Fdsmultiservices** está disponível para atendê-lo:

* 📱 **WhatsApp / Linha Direta:** [(+258) 86 213 4230](https://wa.me/258862134230)
* 📱 **Linha Alternativa:** `(+258) 84 724 0296`
* ✉️ **E-mail:** `fdsmultiservices@gmail.com`
* 🌐 **Portal ZBIZ+ Nuvem:** [http://146.235.224.99/zbiz_plus](http://146.235.224.99/zbiz_plus)
