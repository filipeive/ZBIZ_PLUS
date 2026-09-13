# 📘 Instruções de Instalação e Configuração — ZBIZ+ Enterprise Suite

**Guia Oficial de Instalação, Conexão e Operação (Windows, Linux e Nuvem)**  
**Desenvolvido por:** Fdsmultiservices  
**Suporte Técnico:** (+258) 86 213 4230 | (+258) 84 724 0296 | fdsmultiservices@gmail.com  
**Servidor em Nuvem:** [http://146.235.224.99/zbiz_plus](http://146.235.224.99/zbiz_plus)  

---

## 📑 Sumário

1. [Visão Geral dos Métodos de Instalação](#1-visão-geral-dos-métodos-de-instalação)
2. [Instalação em Computador Windows (Terminal de Balcão / Caixa)](#2-instalação-em-computador-windows-terminal-de-balcão--caixa)
3. [Instalação em Computador Windows como Servidor Local (Offline)](#3-instalação-em-computador-windows-como-servidor-local-offline)
4. [Instalação em Computadores Linux (Ubuntu/Debian/Mint)](#4-instalação-em-computadores-linux-ubuntudebianmint)
5. [Primeiro Acesso: Pré-Registo e Aprovação por SMS](#5-primeiro-acesso-pré-registo-e-aprovação-por-sms)
6. [Ativação da Licença Oficial (`ZBIZ-XXXX-XXXX-XXXX-XXXX`)](#6-ativação-da-licença-oficial-zbiz-xxxx-xxxx-xxxx-xxxx)
7. [Ciclo de Expiração, Modo Somente-Leitura e Reativação](#7-ciclo-de-expiração-modo-somente-leitura-e-reativação)
8. [Configuração de Impressoras Térmicas de Recibos (ESC/POS)](#8-configuração-de-impressoras-térmicas-de-recibos-escpos)
9. [Contactos de Assistência e Suporte](#9-contactos-de-assistência-e-suporte)

---

## 1. Visão Geral dos Métodos de Instalação

O **ZBIZ+** foi desenhado com arquitetura híbrida para se adaptar perfeitamente a qualquer infraestrutura comercial em Moçambique:

| Tipo de Computador | Finalidade | Requisitos de Software | Método Recomendado |
| :--- | :--- | :--- | :--- |
| **Terminal de Caixa / Balcão (Windows)** | Atendimento ao público, vendas no balcão, farmácia, restaurante ou loja conectada à Nuvem. | Apenas Microsoft Edge ou Google Chrome (Nenhum PHP ou MySQL necessário). | **Opção 1**: Executar `install-windows.bat` (Modo App Nativo). |
| **Servidor Local Offline (Windows)** | Estabelecimentos sem internet estável onde o PC atua como servidor central da loja. | Docker Desktop **OU** PHP 8.3 + MySQL (XAMPP). | **Opção 2**: Copiar pasta completa com `vendor/` ou `git clone` e rodar `install-windows.bat`. |
| **Terminal Linux** | Terminais de baixo custo com Ubuntu/Debian/Mint. | Navegador Chrome/Chromium/Firefox. | Executar `./install-linux.sh`. |
| **Acesso Nuvem Direto** | Qualquer telemóvel, tablet, portátil ou PC com navegador. | Qualquer navegador moderno. | Aceder a `http://146.235.224.99/zbiz_plus`. |

---

## 2. Instalação em Computador Windows (Terminal de Balcão / Caixa)

> **Cenário mais comum:** A sua empresa utiliza a Nuvem (`http://146.235.224.99/zbiz_plus`) e deseja transformar o computador de atendimento numa máquina profissional de caixa, sem janelas de navegador, sem abas e com atalho oficial no Ambiente de Trabalho.

### Passo 1: Preparar os Ficheiros
Você **NÃO precisa** copiar o sistema inteiro nem instalar ferramentas pesadas de programação.  
Basta copiar para uma Pen Drive (Flash) ou baixar:
* O ficheiro `install-windows.bat`
* O ficheiro de ícone `public/favicon.ico` (para garantir o logótipo oficial no atalho)

*(Se preferir, pode também copiar a pasta completa do ZBIZ+ para o computador).*

### Passo 2: Executar o Instalador
1. No computador Windows, abra a pasta onde colocou os ficheiros.
2. Clique com o botão direito em **`install-windows.bat`** e selecione **"Executar como Administrador"**.
3. No menu principal, digite **`1`** e pressione **ENTER** (*Configurar Terminal POS Kiosk*).

```
==============================================================================
       ZBIZ+ ENTERPRISE CLOUD & POS SUITE - MOÇAMBIQUE
       Instalador de Terminal de Venda / Farmácia / Restauração
       Desenvolvido por: Fdsmultiservices
==============================================================================

Escolha o modo de instalação desejado:

[1] Configurar Terminal POS Kiosk (Modo App Nativo para Balcão/Caixa)
[2] Instalar Servidor Local Completo (Modo Offline / On-Premise)
[3] Abrir ZBIZ+ Cloud imediatamente no modo App
[0] Sair

Digite a opção desejada [1, 2, 3 ou 0]: 1
```

### Passo 3: Escolher as Preferências do Terminal
* **URL do ZBIZ+**: Pressione **ENTER** para aceitar a Nuvem padrão (`http://146.235.224.99/zbiz_plus`) ou digite o IP local caso use servidor local.
* **Formato de Exibição**:
  - `1` - **Modo App Nativo** *(Recomendado)*: Abre numa janela limpa sem abas nem barra de links, podendo ser redimensionada ou minimizada livremente.
  - `2` - **Modo Kiosk Total**: Trava o ecrã completo, ideal para caixas de supermercado ou postos dedicados onde os operadores não devem aceder ao Windows.
* **Iniciar com o Windows**:
  - Digite `S` para que o ZBIZ+ abra automaticamente sempre que o computador for ligado.

### Passo 4: Conclusão
O instalador gera instantaneamente na sua Área de Trabalho o atalho **"ZBIZ+ Terminal POS"** com o ícone oficial. Ao dar dois cliques, o sistema abre diretamente na tela de login/balcão.

---

## 3. Instalação em Computador Windows como Servidor Local (Offline)

> **Cenário:** A loja ou farmácia fica em uma zona sem cobertura de internet e precisa que o próprio computador Windows processe a base de dados localmente.

### Opção A: Copiar via Pen Drive / Flash (Recomendado para técnicos em campo)
1. No seu computador principal, copie toda a pasta `ZBIZ_PLUS` para a Pen Drive, **garantindo que a pasta `vendor/` está incluída**.
2. Cole a pasta no computador Windows do cliente (ex: `C:\ZBIZ_PLUS`).
3. Instale o **Docker Desktop** (ou o pacote **XAMPP com PHP 8.2/8.3**).
4. Abra a pasta `C:\ZBIZ_PLUS` e execute `install-windows.bat`.
5. Escolha a **Opção [2]** (*Instalar Servidor Local Completo*).
   - Se tiver Docker: O instalador subirá os contentores do PHP e MySQL automaticamente.
   - Se tiver PHP/XAMPP: O instalador inicializará as migrações e o servidor na porta `8000`.

### Opção B: Via Git Clone (Se a máquina tiver acesso temporário à internet)
Abra o terminal (PowerShell) e execute:
```powershell
git clone https://github.com/filipeive/ZBIZ_PLUS.git
cd ZBIZ_PLUS
.\install-windows.bat
```
Escolha a **Opção [2]**.

---

## 4. Instalação em Computadores Linux (Ubuntu/Debian/Mint)

Para computadores operando com Linux no caixa:

1. Abra o terminal na pasta do projeto e torne o script executável:
   ```bash
   chmod +x install-linux.sh
   ./install-linux.sh
   ```
2. O assistente interativo criará o lançador `.desktop` no menu de aplicativos e na Área de Trabalho com ícone e atalho `zbiz-pos`.
3. Pode também iniciar como serviço de fundo automático via `systemctl --user enable zbizplus.service`.

---

## 5. Primeiro Acesso: Pré-Registo e Aprovação por SMS

Para cadastrar uma nova empresa ou filial:

1. **Aceder ao Registo**:
   - Abra o navegador ou o Terminal POS e aceda a `http://146.235.224.99/zbiz_plus/register`.
2. **Preencher os 3 Passos do Pré-Registo**:
   - **Passo 1 (Empresa)**: Nome da Empresa, NUIT, Província/Cidade, Endereço e Ramo de Atividade (*Retalho Geral, Farmácia & Saúde, Restaurante / Bar, Oficina & Auto ou Boutique*).
   - **Passo 2 (Responsável)**: Nome do Gestor, Telemóvel (Moçambique `84/85/86/87/82`) e E-mail.
   - **Passo 3 (Credenciais)**: Criação da Senha de Acesso.
3. **Aprovação de Segurança pela Fdsmultiservices**:
   - Por segurança contra invasões e uso indevido, a conta é criada com status `pending`.
   - O administrador da Fdsmultiservices recebe a notificação, define o período de teste (7, 14, 30 ou 60 dias) e aprova a empresa na Consola do Dono (`/owner/tenants`).
   - O cliente recebe automaticamente um **SMS no seu telemóvel** com as credenciais e o link de acesso liberado.

---

## 6. Ativação da Licença Oficial (`ZBIZ-XXXX-XXXX-XXXX-XXXX`)

Após o período de teste ou mediante pagamento anual/mensal:

1. **Receber a Chave de Licença**:
   - O cliente recebe por SMS ou WhatsApp a sua Chave Serial exclusiva no formato:
     ```
     ZBIZ-XXXX-XXXX-XXXX-XXXX
     ```
   - E pode solicitar o **Certificado Oficial em PDF** timbrado pela Fdsmultiservices.
2. **Ativar no Sistema**:
   - No menu lateral ou clicando no banner superior, aceda a **Activar Licença** (`/license/activate`).
   - Digite ou cole a sua Chave Serial de 16 caracteres.
   - Clique em **Validar e Activar Licença**.
   - O sistema valida a assinatura digital e desbloqueia todas as funcionalidades imediatamente.

---

## 7. Ciclo de Expiração, Modo Somente-Leitura e Reativação

Quando uma licença atinge a data limite sem renovação:

1. **Modo Somente-Leitura Automático**:
   - O cliente **não perde os seus dados**.
   - Permite consultar relatórios, extratos fiscais, faturas passadas e inventário.
   - Todas as operações de escrita (novas vendas no POS, emissão de faturas, alterações de stock) ficam temporariamente bloqueadas.
2. **Alerta no Ecrã**:
   - É exibido um aviso em vermelho no topo com botão direto para **Activar Licença** e botão de **WhatsApp para Suporte Imediato**.
3. **Reativação**:
   - Assim que o pagamento é regularizado, a Fdsmultiservices emite a nova licença ou reativa a conta na consola central. O sistema volta a operar a 100% no mesmo instante.

---

## 8. Configuração de Impressoras Térmicas de Recibos (ESC/POS)

Para imprimir faturas em rolo de 80mm ou 58mm sem abrir janelas de confirmação do Windows:

1. Instale o driver USB da impressora térmica no Windows (ex: POS-80, Epson TM-T20, Xprinter).
2. No Windows, defina essa impressora como **Impressora Padrão**.
3. Quando instalar o ZBIZ+ através do `install-windows.bat`, selecione a **Opção 2 (Modo Kiosk)**.
   - A flag `--kiosk-printing` faz com que o comando de impressão do ZBIZ+ envie o recibo direto para a guilhotina da impressora em menos de 1 segundo.

---

## 9. Contactos de Assistência e Suporte

A equipa técnica da **Fdsmultiservices** está disponível para apoio presencial e remoto:

* **WhatsApp Oficial:** `(+258) 86 213 4230` / `(+258) 84 724 0296`
* **E-mail:** `fdsmultiservices@gmail.com`
* **Localização:** Moçambique
* **Portal Cloud ZBIZ+:** `http://146.235.224.99/zbiz_plus`
