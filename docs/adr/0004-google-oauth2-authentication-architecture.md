# ADR 0004: Arquitetura de Autenticação Google OAuth2 em Ambiente Multi-Tenant & Offline

**Estado:** Aprovado para Desenho (Implementação diferida para Fase 3)  
**Data:** Novembro de 2026  
**Autores:** Equipa de Engenharia ZBIZ+ / Google DeepMind Pair Programming  
**Contexto Técnico:** Laravel 11, Single Database Multi-Tenancy (`tenant_id`), Modos Cloud e Offline (PWA/Kiosk Local)

---

## 1. Contexto e Motivação

Durante a revisão dos fluxos de autenticação (Etapa D2), avaliou-se a introdução do método **"Continuar com Google"** (Google OAuth2 via Laravel Socialite) nas páginas de Login e Pré-Registo do ZBIZ+.

O objetivo é reduzir a fricção de entrada, eliminar a necessidade de memorização de palavras-passe para gestores e aumentar a taxa de conversão no pré-registo empresarial. No entanto, o ZBIZ+ opera sob premissas arquiteturais particulares:
1. **Multi-Tenancy:** Múltiplas empresas partilham a base de dados com isolamento estrito por `tenant_id`.
2. **Modo Híbrido (Cloud vs. Offline/Local):** O sistema corre tanto na Cloud como em quiosques POS locais (terminais de retalho, farmácias e restaurantes em Moçambique com conectividade instável ou nula).
3. **Conformidade Fiscal Moçambicana (Autoridade Tributária - AT):** Todo documento fiscal emitido tem de estar inequivocamente vinculado ao operador humano responsável pelo fecho e abertura do turno de caixa.

---

## 2. Desafios Arquiteturais Identificados

### 2.1. Mapeamento de Utilizador Google para Tenants (Identidade vs. Tenant Scope)
No modelo atual do ZBIZ+, a tabela `users` possui uma restrição de unicidade em `email` e pertence a um `tenant_id` específico (`BelongsToTenant`).
Quando um utilizador autentica via Google (`exemplo@gmail.com`):

* **Cenário A — Registo de Nova Empresa:**  
  O utilizador Google pretende criar uma nova empresa. O fluxo cria um `Tenant` com estado `pending` e associa o utilizador como Administrador. O email da Google torna-se o email do tenant admin.
* **Cenário B — Login de Colaborador Existente:**  
  O utilizador já foi cadastrado por um gestor (ex.: Caixa ou Farmacêutico). O email da Google coincide com o email registado na filial. O sistema autentica-o diretamente no tenant respetivo.
* **Cenário C — Colaborador Multi-Tenant (ex.: Contabilista ou Consultor):**  
  Se um mesmo consultor gere a contabilidade da "Farmácia Muzinga" e do "Supermercado Zambézia" com a mesma conta Google, o sistema não pode assumir arbitrariamente um tenant. É obrigatório apresentar uma tela intermediária de **Seleção de Tenant/Filial** antes de estabelecer a sessão.

### 2.2. Incompatibilidade com o Modo Offline / Kiosks Locais
Nos clientes híbridos (builds Linux, Windows Kiosk e tablets Android operando sem internet):
* O fluxo OAuth2 da Google requer redirecionamento para `accounts.google.com` e comunicação back-channel HTTPS com os servidores da Google para validação do token JWT/OAuth.
* Em balcões sem internet, a autenticação Google **falha imediatamente por falta de conectividade**.
* **Regra:** O modo offline deve continuar a exigir e suportar autenticação local por palavra-passe ou PIN numérico de caixa (sem dependência externa). O botão "Entrar com Google" tem de ser ocultado automaticamente quando `installation_mode === 'offline'`.

### 2.3. Exigências de SMS e Telemóvel em Moçambique
Ao contrário de mercados ocidentais onde o email é o canal primário, em Moçambique:
* O canal oficial de alertas operacionais, recuperação de credenciais e comunicação comercial do ZBIZ+ é o **SMS via rede móvel local (+258 M-Pesa / m-Cel / Vodacom / Movitel)**.
* A autenticação Google fornece apenas nome, email e avatar.
* **Impacto:** O pré-registo via Google não pode ignorar a recolha obrigatória do número de telemóvel (+258) e do NUIT, sob pena de inviabilizar o envio de SMS de ativação e a conformidade fiscal.

---

## 3. Fluxo de Experiência do Utilizador (UX Recomendado)

### 3.1. Fluxo de Login com Google
```
[Ecrã de Login]
       │
       ▼
[Clica em "Continuar com Google"] 
       │
       ▼
[Redireciona para accounts.google.com]
       │
       ▼
[Callback em /auth/google/callback]
       │
       ├─► Caso 1: Email não encontrado na base de dados
       │      └─► Mensagem clara: "Nenhuma empresa associada a este e-mail. 
       │          Deseja efetuar o Pré-Registo da sua empresa?"
       │
       ├─► Caso 2: Utilizador associado a 1 único Tenant ativo
       │      └─► Sessão iniciada com sucesso ──► Redireciona para /dash
       │
       └─► Caso 3: Utilizador associado a Múltiplos Tenants
              └─► Ecrã de Seleção: "Escolha a empresa a aceder" ──► /dash
```

### 3.2. Fluxo de Pré-Registo com Google
1. No Passo 1 do Pré-Registo, o botão *"Preencher dados com a Google"* preenche automaticamente:
   - Nome Completo do Responsável
   - E-mail Corporativo/Pessoal Verificado
2. O utilizador é direcionado para preencher os dados estritamente locais do negócio:
   - Nome da Empresa e Ramo de Atividade (Retalho, Farmácia, Restaurante, Gráfica, Serviços)
   - Telemóvel (+258) obrigatório para receção de SMS
   - Província e NUIT (opcional)
3. Ao submeter, cria o registo com estado `pending` e envia o SMS para a equipa Fdsmultiservices aprovar o teste.

---

## 4. Decisão

> [!IMPORTANT]
> **DECISÃO: DIFERIR A IMPLEMENTAÇÃO DO GOOGLE OAUTH2 PARA A FASE 3.**
> 
> A autenticação com Google está plenamente desenhada e especificada neste documento, mas **NÃO será implementada no código agora**.

### Motivos da Decisão:
1. **Prioridade Operacional:** A prioridade imediata é o redesenho dos ecrãs críticos (Etapa D2: POS, Fecho de Caixa, Vendas, Contas a Receber, Produtos e Definições).
2. **Ambiente Balcão e Caixa:** Mais de 90% das operações diárias de caixa no POS são executadas por operadores de balcão com credenciais simples (login/senha ou PIN), sem necessidade ou uso de contas Google corporativas.
3. **Complexidade Multi-Tenant & Offline:** Implementar Socialite agora exigiria configurar credenciais no Google Cloud Console, ecrãs de consentimento com verificação de domínio oficial, gestão de cookies entre subdomínios e testes de contingência para nós offline, o que desviaria o foco da estabilização da versão 2.0.

---

## 5. Especificação Técnica para Implementação Futura (Fase 3)

Quando a implementação for iniciada na Fase 3, os seguintes passos mecânicos deverão ser executados:

1. **Dependência Composer:**
   ```bash
   composer require laravel/socialite
   ```
2. **Migração na base de dados:**
   Adicionar colunas na tabela `users`:
   ```php
   $table->string('google_id')->nullable()->index();
   $table->string('google_avatar')->nullable();
   ```
3. **Variáveis de Ambiente (`.env`):**
   ```env
   GOOGLE_CLIENT_ID=seu-client-id.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=seu-client-secret
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```
4. **Controlador:**
   Criar `app/Http/Controllers/Auth/GoogleAuthController.php`:
   - `redirectToGoogle()`
   - `handleGoogleCallback()`
5. **Guard Offline:**
   Garantir a verificação:
   ```php
   if (config('app.installation_mode') === 'offline') {
       return redirect()->route('login')->with('error', 'Autenticação Google não permitida em servidores offline.');
   }
   ```
