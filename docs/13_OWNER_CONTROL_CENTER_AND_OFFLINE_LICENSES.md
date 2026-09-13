# OWNER CONTROL CENTER & GESTÃO DE LICENÇAS SaaS / OFFLINE
## `docs/13_OWNER_CONTROL_CENTER_AND_OFFLINE_LICENSES.md`

---

## 1. VISÃO GERAL DA ARQUITETURA SAAS DO DONO

O **ZBIZ+** foi concebido para suportar tanto o modelo de subscrição na Nuvem (**Cloud SaaS**) quanto o modelo de implantação local isolada (**On-Premises / Offline**).

A área de gestão do dono da plataforma é totalmente restrita a utilizadores com privilégio `super_admin`:

```text
URL do Painel do Dono: /owner/tenants
```

---

## 2. RECURSOS DO CONTROL CENTER DO DONO (SUPER ADMIN)

### A. Dashboard Executivo com Métricas SaaS em Tempo Real
1. **MRR (Monthly Recurring Revenue):** Cálculo dinâmico da receita mensal recorrente somando todas as subscrições ativas (`SUM(plans.monthly_price)`).
2. **ARR Projetado (Annual Recurring Revenue):** Estimativa anualizada da receita do ecossistema (`MRR * 12`).
3. **Total de Empresas & Taxa de Ativação:** Contagem separada de empresas Ativas, em Avaliação (Trial), Suspensas e Canceladas.
4. **Instalações Offline vs Cloud:** Rastreio de instâncias a correr localmente sem internet permanente.
5. **Licenças a Expirar (Próximos 30 dias):** Radar de renovações comerciais para contacto proativo de vendas.
6. **Estatísticas do Ecossistema:** Total consolidado de filiais ativas, utilizadores e artigos em todos os clientes.

### B. Chaves de Licença em Formato Padrão de Software
As licenças agora utilizam chaves seriais de 24 caracteres fáceis de ditar, imprimir e colar:

```text
Formato Oficial: ZBIZ-XXXX-XXXX-XXXX-XXXX
Exemplo Real:    ZBIZ-4F92-K81M-Q7P3-9A2E
```

* **Cópia em 1 Clique:** Na própria tabela de tenants (`/owner/tenants`), cada cliente exibe a chave serial ativa com botão interativo de cópia instantânea (com feedback visual "Copiado!").
* **Validação Dupla:** A tela de ativação de licença aceita tanto a Chave Serial legível (`ZBIZ-...`) quanto o Token Criptográfico longo assinado (`payload.hmac_sha256`).

### C. Certificado Oficial de Licença de Software (Impressão & PDF)
O sistema emite uma página de **Certificado Oficial de Licença** (`/owner/tenants/{tenant}/licenses/{license}/certificate`) que inclui:
* Selo de Autenticidade e Logotipo Oficial ZBIZ+ Enterprise;
* Identificação da Entidade Licenciada (Nome da Empresa, NUIT, Setor de Atividade);
* Especificações do Plano (Nome do Plano, Modo Cloud ou Offline, Limite de Filiais/Utilizadores);
* Chave Serial de Ativação em destaque monoespaçado de grande porte;
* Período de Validade (Data de Início e Expiração);
* Assinatura Digital de Autenticidade (Hash HMAC SHA-256);
* Botão nativo de **Imprimir / Guardar como PDF** (`window.print()`).

### D. Modal de Registo Rápido de Novas Empresas (Onboarding Expresso)
No topo do Control Center, o botão `+ Registar Nova Empresa` abre um modal que cria todo o ecossistema do cliente em uma única submissão:
1. Criação dos dados comerciais do **Tenant** (Nome, Setor, Email, Telefone, NUIT);
2. Associação do **Plano SaaS** e Modo de Instalação (Cloud, Local com Internet ou Offline);
3. Criação da **Filial Sede Principal** (`is_main = true`);
4. Criação do utilizador **Administrador Inicial** do cliente com credenciais seguras;
5. Criação da **Subscrição Ativa** e emissão imediata da primeira **Chave de Licença Serial**.

### E. Modo Suporte Técnico Instantâneo (Impersonate)
O dono ou engenheiro de suporte pode alternar com 1 clique para o ambiente operacional de qualquer cliente para prestar assistência técnica:
* Ao clicar no botão **`Suporte`** ao lado do tenant, a sessão é configurada com `current_tenant_id` e `current_branch_id`.
* O sistema exibe uma **Barra Superior Amarela Persistente**:
  `⚠️ MODO SUPORTE TÉCNICO ATIVO: A operar na empresa [Nome] (Filial: Sede)  [ Sair do Suporte & Voltar ao Owner ]`
* O operador de suporte tem visão exata de tudo o que o cliente vê (Frente de Caixa, Estoque, Relatórios, etc.).
* Ao clicar em `Sair do Suporte`, a sessão é limpa e retorna instantaneamente ao Control Center.

---

## 3. FLUXO DE ATIVAÇÃO OFFLINE

1. O Dono emite a chave serial no Control Center para a empresa e imprime/envia o Certificado de Licença.
2. Na máquina local da farmácia ou loja (mesmo sem internet), o cliente abre `/license/activate`.
3. O cliente insere a chave serial `ZBIZ-XXXX-XXXX-XXXX-XXXX`.
4. O motor `LicenseService` valida a chave e a assinatura criptográfica e desbloqueia o sistema imediatamente com todos os limites contratados.

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: fdev-ms (FDS Multiservices)**  
*Engenharia de Software & Suporte Técnico:* `fdev-ms@fdevms:~/Filipe/reprosys$`  
*Contacto & Assistência:* (+258) 84 999 1122 · Quelimane / Moçambique  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
