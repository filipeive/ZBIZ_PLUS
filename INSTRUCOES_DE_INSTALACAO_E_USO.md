# 📘 Instruções de Instalação e Configuração — ZBIZ+ Enterprise Suite

**Guia Oficial de Instalação, Onboarding Limpo, Operação e Desinstalação (Windows, Linux e Nuvem)**  
**Desenvolvido por:** Fdsmultiservices  
**Suporte Técnico:** (+258) 86 213 4230 | (+258) 84 724 0296 | fdsmultiservices@gmail.com  
**Servidor em Nuvem:** [http://146.235.224.99/zbiz_plus](http://146.235.224.99/zbiz_plus)  

---

## 📑 Sumário

1. [Visão Geral dos Métodos de Instalação](#1-visão-geral-dos-métodos-de-instalação)
2. [Instalação Limpa para Novos Clientes (Farmácias / Empresas) — Zero Dados de Teste](#2-instalação-limpa-para-novos-clientes-farmácias--empresas--zero-dados-de-teste)
3. [Instalação em Computador Windows (Terminal de Balcão / Caixa)](#3-instalação-em-computador-windows-terminal-de-balcão--caixa)
4. [Instalação em Computador Windows como Servidor Local (Offline)](#4-instalação-em-computador-windows-como-servidor-local-offline)
5. [Instalação em Computadores Linux (Ubuntu/Debian/Mint)](#5-instalação-em-computadores-linux-ubuntudebianmint)
6. [Como Desinstalar o ZBIZ+ (Linux e Windows)](#6-como-desinstalar-o-zbiz-linux-e-windows)
7. [Primeiro Acesso: Pré-Registo e Ativação Instantânea](#7-primeiro-acesso-pré-registo-e-ativação-instantânea)
8. [Ativação da Licença Oficial (`ZBIZ-XXXX-XXXX-XXXX-XXXX`)](#8-ativação-da-licença-oficial-zbiz-xxxx-xxxx-xxxx-xxxx)
9. [Ciclo de Expiração, Modo Somente-Leitura e Reativação](#9-ciclo-de-expiração-modo-somente-leitura-e-reativação)
10. [Configuração de Impressoras Térmicas de Recibos (ESC/POS)](#10-configuração-de-impressoras-térmicas-de-recibos-escpos)
11. [Contactos de Assistência e Suporte](#11-contactos-de-assistência-e-suporte)

---

## 1. Visão Geral dos Métodos de Instalação

O **ZBIZ+** foi desenhado com arquitetura híbrida para se adaptar perfeitamente a qualquer infraestrutura comercial em Moçambique:

| Tipo de Instalação | Finalidade | Requisitos de Software | Método Recomendado |
| :--- | :--- | :--- | :--- |
| **Terminal de Caixa / Balcão (Windows)** | Atendimento ao público, vendas no balcão de Farmácias ou Lojas conectadas à Nuvem ou Servidor Local. | Apenas Microsoft Edge ou Google Chrome (Nenhum PHP ou MySQL necessário). | **Opção 1**: Executar `install-windows.bat` (Modo App Nativo ou Kiosk). |
| **Servidor Local Offline (Windows / Linux)** | Estabelecimentos sem internet estável onde o PC atua como servidor central da farmácia/loja. | Docker Desktop **OU** PHP 8.3 + MySQL. | **Opção 2**: Copiar pasta do projeto e executar `install-windows.bat` ou `install-linux.sh`. |
| **Terminal Linux de Balcão** | Terminais de baixo custo com Ubuntu/Debian/Mint. | Navegador Chrome/Chromium/Firefox. | Executar `./install-linux.sh` (Opção 1). |
| **Acesso Nuvem Direto** | Gestão pelo proprietário via telemóvel, tablet ou portátil de qualquer lugar. | Qualquer navegador moderno. | Aceder a `http://146.235.224.99/zbiz_plus`. |

---

## 2. Instalação Limpa para Novos Clientes (Farmácias / Empresas) — Zero Dados de Teste

> [!IMPORTANT]
> Quando você vai a um cliente real (ex: uma Farmácia ou Drogaria), o sistema **nunca deve conter produtos, vendas, clientes ou dívidas de teste**. O ZBIZ+ dispõe de mecanismos nativos para garantir uma instalação 100% limpa e profissional.

### A. Cenário Nuvem (SaaS Multi-Tenant)
*(A Farmácia vai usar o ZBIZ+ hospedado na Nuvem central: `http://146.235.224.99/zbiz_plus`)*

O ZBIZ+ possui isolamento completo por `tenant_id`. **Não precisa apagar o banco de dados nem mexer nos outros clientes!**

1. **Registar a Nova Farmácia**:
   - Aceda a `http://146.235.224.99/zbiz_plus/register` (ou crie na consola de SuperAdmin `/superadmin`).
   - Insira os dados reais da empresa:
     - **Nome da Empresa**: ex: *Farmácia Central de Bilene*
     * **Ramo de Atividade**: Selecione **Farmácia**
     * **Plano Desejado**: **ZBIZ Pharmacy+** (ou Pro)
     * **NUIT, Província, Cidade, Nome do Responsável Técnico, E-mail e Senha**.
2. **O que nasce automaticamente para a Farmácia**:
   - **Zero Vendas, Zero Dívidas e Zero Clientes**: Todos os módulos iniciam completamente vazios.
   - **Zero Produtos de Teste**: Nenhum medicamento fictício é inserido no inventário.
   - **Contas de Caixa Zeradas**: Cria automaticamente as contas "Caixa Principal" (Saldo: 0,00 MT) e "Carteira Móvel M-Pesa / e-Mola" (Saldo: 0,00 MT).
   - **Catálogo de Categorias Farmacêuticas ANARME**: Já vêm cadastradas as categorias oficiais para organizar os medicamentos:
     - *Antibióticos & Antimicrobianos*
     - *Analgésicos & Anti-inflamatórios*
     - *Vitaminas & Suplementos*
     - *Medicamentos Pediátricos*
     - *Dermocosmética & Cuidados*
     - *Material Hospitalar & Socorros*
     - *Psicotrópicos & Controlados (Registo ANARME)*
3. **No computador do Caixa / Balcão da Farmácia**:
   - Basta rodar o `install-windows.bat` (Opção 1) ou `install-linux.sh` (Opção 1) apontando para a Nuvem.

---

### B. Cenário Servidor Local / Offline (Instalação On-Premise)
*(O cliente comprou o sistema para rodar localmente no servidor/computador da sua própria farmácia)*

Se você estiver instalando um servidor dedicado novo no cliente:

#### Passo 1: Inicializar Banco de Dados Limpo
No terminal da pasta do ZBIZ+:
```bash
# 1. Cria todas as tabelas em branco (elimina qualquer dado residual)
php artisan migrate:fresh --force

# 2. Popula APENAS os planos comerciais e perfis de acesso (SEM utilizadores de teste)
php artisan db:seed --force
```

> [!NOTE]
> O arquivo `DatabaseSeeder.php` do ZBIZ+ foi programado para **nunca semear utilizadores nem produtos fictícios**. Os seeders com dados demonstrativos (`FarmaciaCentralSeeder`, `FarmaciaMuzingaDemoSeeder`) são isolados e nunca rodam por padrão.

#### Passo 2: Criar a Farmácia Oficial em 30 Segundos via Terminal
Execute o comando oficial de onboarding:
```bash
php artisan zbiz:new-tenant
```
O assistente interativo do terminal solicitará os dados da farmácia:
```
====================================================================
   ZBIZ+ Enterprise Suite - Criador de Instalação Limpa de Cliente
====================================================================

Nome comercial da Empresa / Farmácia: Farmácia Esperança
Ramo de atividade: [0] pharmacy
NUIT da empresa: 400123456
Província [Maputo Cidade]: Gaza
Cidade / Distrito [Maputo]: Chókwè
Nome completo do Administrador / Farmacêutico: Dr. Américo Macuácua
E-mail de acesso do Administrador: admin@farmaciaesperanca.co.mz
Senha de acesso: **********
Telemóvel do responsável: +258 84 123 4567
Plano [pharmacy_plus]: pharmacy_plus
```

**Resultado Imediato:**
* Cria a Farmácia ativa (`status = active`).
* Cria o utilizador Administrador pronto a entrar (`is_active = true`).
* Ativa a licença local por 365 dias (`subscription_ends_at = 1 ano`).
* Cria as categorias de farmácia ANARME e os caixas financeiros zerados.
* Deixa o inventário de produtos e o histórico de vendas **100% limpos**.

---

## 3. Instalação em Computador Windows (Terminal de Balcão / Caixa)

> **Cenário mais comum:** A sua empresa utiliza a Nuvem (`http://146.235.224.99/zbiz_plus`) ou Servidor Local e deseja transformar o computador de atendimento numa máquina profissional de caixa, sem janelas de navegador, sem abas e com atalho oficial no Ambiente de Trabalho.

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
[4] Desinstalar Terminal e Serviços do ZBIZ+
[0] Sair

Digite a opção desejada [1, 2, 3, 4 ou 0]: 1
```

### Passo 3: Escolher as Preferências do Terminal
* **URL do ZBIZ+**: Pressione **ENTER** para aceitar a Nuvem padrão (`http://146.235.224.99/zbiz_plus`) ou digite o IP local caso use servidor local (ex: `http://localhost:8000` ou `http://192.168.1.100:8000`).
* **Formato de Exibição**:
  - `1` - **Modo App Nativo** *(Recomendado)*: Abre numa janela limpa sem abas nem barra de links, podendo ser redimensionada ou minimizada livremente.
  - `2` - **Modo Kiosk Total**: Trava o ecrã completo, ideal para caixas de supermercado ou postos dedicados onde os operadores não devem aceder ao Windows.
* **Iniciar com o Windows**:
  - Digite `S` para que o ZBIZ+ abra automaticamente sempre que o computador for ligado.

### Passo 4: Conclusão
O instalador gera instantaneamente na sua Área de Trabalho o atalho **"ZBIZ+ Terminal POS"** com o ícone oficial. Ao dar dois cliques, o sistema abre diretamente na tela de login/balcão.

---

## 4. Instalação em Computador Windows como Servidor Local (Offline)

> **Cenário:** A loja ou farmácia fica em uma zona sem cobertura de internet e precisa que o próprio computador Windows processe a base de dados localmente.

### Opção A: Copiar via Pen Drive / Flash (Recomendado para técnicos em campo)
1. No seu computador principal, copie toda a pasta `ZBIZ_PLUS` para a Pen Drive, **garantindo que a pasta `vendor/` está incluída**.
2. Cole a pasta no computador Windows do cliente (ex: `C:\ZBIZ_PLUS`).
3. Instale o **Docker Desktop** (ou o pacote **PHP 8.3 + MySQL / XAMPP**).
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

## 5. Instalação em Computadores Linux (Ubuntu/Debian/Mint)

Para postos de venda operando com Linux no balcão:

1. Abra o terminal na pasta do projeto e torne os scripts executáveis:
   ```bash
   chmod +x install-linux.sh uninstall-linux.sh
   ./install-linux.sh
   ```
2. Escolha a **Opção [1]** para configurar o Terminal POS Kiosk.
3. O assistente criará automaticamente o atalho na Área de Trabalho (`ZBIZ+ Terminal POS`) e no menu de aplicativos (`zbiz-plus.desktop`), com permissões confiadas.
4. Para servidor local completo em Linux, escolha a **Opção [2]** (Docker ou serviço background via systemd).

---

## 6. Como Desinstalar o ZBIZ+ (Linux e Windows)

Se você realizou testes num computador Linux ou Windows e precisa remover o terminal, os atalhos e os serviços de segundo plano:

### No Linux:

#### Método Automático 1 (Recomendado):
Execute o script dedicado de desinstalação:
```bash
./uninstall-linux.sh
```
Ou execute `./install-linux.sh` e escolha a **Opção [4]** (*Desinstalar Terminal e Serviços do ZBIZ+ deste computador*).

#### Método Manual via Linha de Comando:
Basta rodar no seu terminal os seguintes comandos:
```bash
# 1. Remover atalhos da Área de Trabalho e do Menu de Aplicações
rm -f "$HOME/Desktop/ZBIZ+ Terminal POS.desktop"
rm -f "$HOME/Área de Trabalho/ZBIZ+ Terminal POS.desktop"
rm -f "$HOME/.local/share/applications/zbiz-plus.desktop"

# 2. Remover inicialização automática ao ligar o PC
rm -f "$HOME/.config/autostart/zbiz-plus.desktop"

# 3. Encerrar e remover serviço de servidor local (se tiver usado a Opção 2)
systemctl --user stop zbizplus.service 2>/dev/null || true
systemctl --user disable zbizplus.service 2>/dev/null || true
rm -f "$HOME/.config/systemd/user/zbizplus.service"
systemctl --user daemon-reload 2>/dev/null || true
```

---

### No Windows:

1. **Remover o Atalho**:
   - Vá à sua **Área de Trabalho** e elimine o ficheiro `ZBIZ+ Terminal POS.lnk` (Shift + Delete).
2. **Remover a Inicialização Automática (se ativada)**:
   - Pressione as teclas `Win + R`, digite `shell:startup` e pressione **ENTER**.
   - Se existir o atalho `ZBIZ+ Terminal POS`, elimine-o.
3. **Remover o Script Auxiliar**:
   - Elimine o arquivo `C:\Users\SEU_USUARIO\ZBIZ_Launcher.bat`.

---

## 7. Primeiro Acesso: Pré-Registo e Ativação Instantânea

Para cadastrar uma nova empresa ou filial pela interface web:

1. **Aceder ao Registo**:
   - Abra o navegador ou o Terminal POS e aceda a `http://146.235.224.99/zbiz_plus/register`.
2. **Preencher os 3 Passos do Pré-Registo**:
   - **Passo 1 (Empresa)**: Nome da Empresa, NUIT, Província/Cidade, Endereço e Ramo de Atividade (*Farmácia & Saúde, Retalho Geral, Restaurante / Bar, Oficina & Auto ou Boutique*).
   - **Passo 2 (Responsável)**: Nome do Gestor, Telemóvel (Moçambique `84/85/86/87/82`) e E-mail.
   - **Passo 3 (Credenciais)**: Criação da Senha de Acesso.
3. **Aprovação / Ativação**:
   - Se for na Nuvem: O administrador aprova a conta na Consola Central (`/owner/tenants`) ou via SMS automático.
   - Se for no Servidor Local: Usando o comando `php artisan zbiz:new-tenant`, o utilizador já nasce **100% ativo** e com acesso imediato ao login!

---

## 8. Ativação da Licença Oficial (`ZBIZ-XXXX-XXXX-XXXX-XXXX`)

Após o período de teste ou mediante contratação anual/mensal:

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

## 9. Ciclo de Expiração, Modo Somente-Leitura e Reativação

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

## 10. Configuração de Impressoras Térmicas de Recibos (ESC/POS)

Para imprimir faturas em rolo de 80mm ou 58mm sem abrir janelas de confirmação do Windows:

1. Instale o driver USB da impressora térmica no Windows (ex: POS-80, Epson TM-T20, Xprinter).
2. No Windows, defina essa impressora como **Impressora Padrão**.
3. Quando instalar o ZBIZ+ através do `install-windows.bat`, selecione a **Opção 2 (Modo Kiosk)**.
   - A flag `--kiosk-printing` faz com que o comando de impressão do ZBIZ+ envie o recibo direto para a guilhotina da impressora em menos de 1 segundo.
4. **Logótipo nos Recibos**: Nas Definições da Empresa (`/settings`), faça upload do logótipo com fundo transparente ou branco. Ele sairá impresso no cabeçalho dos talões térmicos e faturas A4.

---

## 11. Contactos de Assistência e Suporte

A equipa técnica da **Fdsmultiservices** está disponível para apoio presencial e remoto:

* **WhatsApp Oficial:** `(+258) 86 213 4230` / `(+258) 84 724 0296`
* **E-mail:** `fdsmultiservices@gmail.com`
* **Localização:** Moçambique
* **Portal Cloud ZBIZ+:** `http://146.235.224.99/zbiz_plus`
