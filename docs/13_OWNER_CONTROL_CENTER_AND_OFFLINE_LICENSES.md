# Owner Control Center e Licenças Offline
# OWNER CONTROL CENTER & GESTÃO DE LICENÇAS SaaS / OFFLINE
## `docs/13_OWNER_CONTROL_CENTER_AND_OFFLINE_LICENSES.md`

## Objetivo
---

O ZBIZ+ deve suportar dois modelos comerciais:
## 1. VISÃO GERAL DA ARQUITETURA SAAS DO DONO

- **SaaS/Cloud**: o cliente usa a instalação central, com subscrição validada diretamente no servidor.
- **Instalação local/offline**: o cliente instala o sistema na própria máquina ou rede local, e ativa a aplicação com uma licença assinada emitida pelo dono do sistema.
O **ZBIZ+** foi concebido para suportar tanto o modelo de subscrição na Nuvem (**Cloud SaaS**) quanto o modelo de implantação local isolada (**On-Premises / Offline**).

Esta separação evita misturar gestão comercial com operação do tenant. O cliente gere vendas, stock, finanças e equipa; o dono do sistema gere clientes, planos, módulos, limites e validade.
A área de gestão do dono da plataforma é totalmente restrita a utilizadores com privilégio `super_admin`:

## Componentes

### Control Center do Dono

Área restrita a utilizadores `super_admin`, disponível em:

```text
/owner/tenants
URL do Painel do Dono: /owner/tenants
```

Responsabilidades:
---

- listar e pesquisar tenants/clientes;
- ver estado, plano, modo de instalação e validade;
- atualizar dados comerciais do tenant;
- trocar plano e estado da subscrição;
- definir modo `cloud`, `local_online` ou `offline`;
- emitir e revogar chaves de licença.
## 2. RECURSOS DO CONTROL CENTER DO DONO (SUPER ADMIN)

### Subscrição
### A. Dashboard Executivo com Métricas SaaS em Tempo Real
1. **MRR (Monthly Recurring Revenue):** Cálculo dinâmico da receita mensal recorrente somando todas as subscrições ativas (`SUM(plans.monthly_price)`).
2. **ARR Projetado (Annual Recurring Revenue):** Estimativa anualizada da receita do ecossistema (`MRR * 12`).
3. **Total de Empresas & Taxa de Ativação:** Contagem separada de empresas Ativas, em Avaliação (Trial), Suspensas e Canceladas.
4. **Instalações Offline vs Cloud:** Rastreio de instâncias a correr localmente sem internet permanente.
5. **Licenças a Expirar (Próximos 30 dias):** Radar de renovações comerciais para contacto proativo de vendas.
6. **Estatísticas do Ecossistema:** Total consolidado de filiais ativas, utilizadores e artigos em todos os clientes.

A tabela `subscriptions` continua a representar o ciclo comercial online:
### B. Chaves de Licença em Formato Padrão de Software
As licenças agora utilizam chaves seriais de 24 caracteres fáceis de ditar, imprimir e colar:

- plano contratado;
- estado da subscrição;
- período atual;
- método/referência de pagamento.

O middleware `subscription` bloqueia escrita quando a subscrição ou licença expira, mantendo leitura em modo somente-leitura.

### Licença

A tabela `license_keys` guarda o histórico das chaves emitidas:

- tenant;
- plano;
- modo de instalação;
- validade;
- hash da chave;
- assinatura;
- estado: `issued`, `active`, `revoked`, `expired`.

A chave entregue ao cliente é um token:

```text
base64url(payload).assinatura_hmac
Formato Oficial: ZBIZ-XXXX-XXXX-XXXX-XXXX
Exemplo Real:    ZBIZ-4F92-K81M-Q7P3-9A2E
```

O payload inclui tenant, plano, features, limites e datas. A assinatura usa `LICENSE_SIGNING_KEY`, permitindo validação offline sem consultar o servidor central.
* **Cópia em 1 Clique:** Na própria tabela de tenants (`/owner/tenants`), cada cliente exibe a chave serial ativa com botão interativo de cópia instantânea (com feedback visual "Copiado!").
* **Validação Dupla:** A tela de ativação de licença aceita tanto a Chave Serial legível (`ZBIZ-...`) quanto o Token Criptográfico longo assinado (`payload.hmac_sha256`).

## Fluxo Cloud
### C. Certificado Oficial de Licença de Software (Impressão & PDF)
O sistema emite uma página de **Certificado Oficial de Licença** (`/owner/tenants/{tenant}/licenses/{license}/certificate`) que inclui:
* Selo de Autenticidade e Logotipo Oficial ZBIZ+ Enterprise;
* Identificação da Entidade Licenciada (Nome da Empresa, NUIT, Setor de Atividade);
* Especificações do Plano (Nome do Plano, Modo Cloud ou Offline, Limite de Filiais/Utilizadores);
* Chave Serial de Ativação em destaque monoespaçado de grande porte;
* Período de Validade (Data de Início e Expiração);
* Assinatura Digital de Autenticidade (Hash HMAC SHA-256);
* Botão nativo de **Imprimir / Guardar como PDF** (`window.print()`).

1. Dono cria ou aprova tenant.
2. Define plano e validade no Control Center.
3. Sistema atualiza a subscrição do tenant.
4. O tenant usa apenas os módulos permitidos pelo plano.
### D. Modal de Registo Rápido de Novas Empresas (Onboarding Expresso)
No topo do Control Center, o botão `+ Registar Nova Empresa` abre um modal que cria todo o ecossistema do cliente em uma única submissão:
1. Criação dos dados comerciais do **Tenant** (Nome, Setor, Email, Telefone, NUIT);
2. Associação do **Plano SaaS** e Modo de Instalação (Cloud, Local com Internet ou Offline);
3. Criação da **Filial Sede Principal** (`is_main = true`);
4. Criação do utilizador **Administrador Inicial** do cliente com credenciais seguras;
5. Criação da **Subscrição Ativa** e emissão imediata da primeira **Chave de Licença Serial**.

## Fluxo Offline
### E. Modo Suporte Técnico Instantâneo (Impersonate)
O dono ou engenheiro de suporte pode alternar com 1 clique para o ambiente operacional de qualquer cliente para prestar assistência técnica:
* Ao clicar no botão **`Suporte`** ao lado do tenant, a sessão é configurada com `current_tenant_id` e `current_branch_id`.
* O sistema exibe uma **Barra Superior Amarela Persistente**:
  `⚠️ MODO SUPORTE TÉCNICO ATIVO: A operar na empresa [Nome] (Filial: Sede)  [ Sair do Suporte & Voltar ao Owner ]`
* O operador de suporte tem visão exata de tudo o que o cliente vê (Frente de Caixa, Estoque, Relatórios, etc.).
* Ao clicar em `Sair do Suporte`, a sessão é limpa e retorna instantaneamente ao Control Center.

1. Dono abre o tenant em `/owner/tenants/{tenant}`.
2. Escolhe plano, modo `offline`, início e expiração.
3. Emite a chave.
4. Cliente cola a chave em `/license/activate` na instalação local.
5. A instalação local valida a assinatura e ativa:
   - tenant;
   - plano;
   - features;
   - limites;
   - expiração.
---

## Segurança
## 3. FLUXO DE ATIVAÇÃO OFFLINE

- A licença não deve ser editável manualmente pelo cliente.
- `LICENSE_SIGNING_KEY` deve ser diferente do `APP_KEY` em produção.
- A chave privada/segredo deve ficar apenas com o dono do sistema.
- Revogar licença funciona imediatamente no modo cloud/local-online; em instalações totalmente offline, a revogação só é aplicada quando houver nova ativação, atualização manual ou sincronização.
1. O Dono emite a chave serial no Control Center para a empresa e imprime/envia o Certificado de Licença.
2. Na máquina local da farmácia ou loja (mesmo sem internet), o cliente abre `/license/activate`.
3. O cliente insere a chave serial `ZBIZ-XXXX-XXXX-XXXX-XXXX`.
4. O motor `LicenseService` valida a chave e a assinatura criptográfica e desbloqueia o sistema imediatamente com todos os limites contratados.

## Configuração
---

Adicionar no `.env` do servidor emissor e das instalações locais:

```env
LICENSE_ISSUER=zbiz-plus-owner
LICENSE_SIGNING_KEY=uma-chave-grande-e-secreta
LICENSE_CLOCK_SKEW_MINUTES=10
```

Para instalações offline, o `LICENSE_SIGNING_KEY` precisa ser o mesmo usado para emitir a chave. Numa evolução futura, o ideal é migrar de HMAC para assinatura assimétrica, onde o servidor emissor guarda a chave privada e a instalação local recebe apenas a chave pública.

## Regras de Feature

As rotas usam middleware por feature:

```text
feature:pos
feature:sales
feature:stock_basic
feature:cash_management
feature:debts
feature:salaries
feature:multi_branch
feature:reports_advanced
```

O `SubscriptionService` mantém aliases para planos antigos:

- `stock_basic` também aceita `inventory`;
- `cash_management` também aceita `finance`;
- `reports_advanced` também aceita `reports`;
- `pharmacy` também aceita `pharmacy_anarme`;
- `sales` também aceita `pos`.

## Próximas Evoluções

- criar edição completa de planos pelo Control Center;
- adicionar export/import de pacote de instalação offline;
- adicionar sincronização opcional quando a instalação local tiver internet;
- trocar HMAC por chave pública/privada;
- adicionar auditoria de emissão, download, ativação e revogação.
────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: Fdsmultiservices**  
*WhatsApp & Suporte Técnico:* (+258) 86 213 4230 · Quelimane / Moçambique  
*Email:* `fdsmultiservices@gmail.com`  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
