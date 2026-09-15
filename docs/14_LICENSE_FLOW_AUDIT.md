# AUDITORIA TÉCNICA DO FLUXO DE LICENCIAMENTO (PONTA-A-PONTA)
## `docs/14_LICENSE_FLOW_AUDIT.md`

# Auditoria Técnica: Fluxo de Licenciamento Ponta-a-Ponta (SaaS & Offline)
## Documento Oficial: `docs/14_LICENSE_FLOW_AUDIT.md`
**Data da Auditoria:** 15 de Setembro de 2026  
**Auditor:** Equipa de Engenharia / Antigravity Agentic Pair  
**Destinatário:** Filipe / Dono do Produto ZBIZ+ & Fdsmultiservices  
**Estado:** Documento de Análise & Proposta Arquitetural (Sem alterações de código prematuras)
**Sistema:** ZBIZ+ (Enterprise Multi-Tenant SaaS & On-Premises)  
**Versão Auditada:** v1.0.21  
**Autor:** Engenharia de Plataforma ZBIZ+ / Fdsmultiservices  

---

## SUMÁRIO EXECUTIVO
## 1. Sumário Executivo

O **ZBIZ+** possui uma arquitetura híbrida projetada para suportar tanto clientes centralizados em nuvem (**Cloud SaaS**) quanto clientes com implantação local isolada (**On-Premises / Offline**), como farmácias e postos de venda em locais com instabilidade de conectividade.
O **ZBIZ+** foi concebido para atender à realidade de Moçambique e mercados emergentes, suportando dois modos de operação:
1. **Cloud SaaS**: Aplicação centralizada em nuvem com subscrições gerenciadas online.
2. **On-Premises / Offline**: Instalação dedicada no cliente (ex: farmácias com conectividade instável), funcionando autonomamente com chaves seriais legíveis (`ZBIZ-XXXX-XXXX-XXXX-XXXX`) e tokens criptográficos assinados via HMAC-SHA256.

Esta auditoria analisa exaustivamente o ciclo de vida das licenças no sistema:
1. Emissão e Estrutura Criptográfica do Token;
2. Vínculo de Máquina (Hardware Binding / Node Locking);
3. Dinâmica de Revogação em Cenários Desconectados;
4. UX e Ciclo de Vida da Expiração no Front-End e POS;
5. Mecanismos de Auditoria e Rastreabilidade Forense;
6. Experiência do Utilizador e Tratamento de Exceções.
Esta auditoria técnica avaliou o ciclo de vida completo das licenças: **Emissão no Control Center ➔ Ativação pelo Cliente ➔ Validação Criptográfica ➔ Enforcing de Validade ➔ Revogação ➔ Auditoria & Rastreabilidade**.

O resultado demonstra que a arquitetura criptográfica é sólida e matematicamente inviolável sem a `LICENSE_SIGNING_KEY`, mas identifica oportunidades críticas de melhoria na experiência do utilizador (alertas prévios de expiração) e no controlo contra replicação de nós em máquinas estritamente offline.

---

## 1. VÍNCULO DE ATIVAÇÃO E MECANISMO DE ASSINATURA CRIPTOGRÁFICA
## 2. Análise Detalhada dos 5 Pilares de Licenciamento

### 1.1. Como Opera a Assinatura Atual (`LicenseService`)
Atualmente, o método `LicenseService::issue()` gera uma assinatura digital HMAC baseada no algoritmo SHA-256:
### 2.1. Vínculo de Ativação & Anti-Pirataria (`LicenseService::activateForTenant`)

```php
$payload = [
    'issuer'   => config('license.issuer'), // ex: zbiz-plus-owner
    'version'  => 1,
    'mode'     => $mode, // 'offline', 'cloud', 'local_online'
    'tenant'   => [
        'id'            => $tenant->id,
        'slug'          => $tenant->slug,
        'name'          => $tenant->name,
        'business_type' => $tenant->business_type,
    ],
    'plan'     => [
        'id'           => $plan->id,
        'slug'         => $plan->slug,
        'name'         => $plan->name,
        'features'     => $plan->features ?? [],
        'max_branches' => $plan->max_branches,
        'max_users'    => $plan->max_users,
        'max_products' => $plan->max_products,
    ],
    'starts_at' => $startsAt->toIso8601String(),
    'expires_at' => $expiresAt->toIso8601String(),
    'issued_at' => now()->toIso8601String(),
    'nonce'     => (string) Str::uuid(),
];
#### Como Funciona Atualmente:
- No Control Center (`/owner/tenants`), o Super Admin emite uma licença através de `LicenseService::issue()`.
- O payload inclui os dados do tenant (`id`, `slug`, `name`, `business_type`), plano (`features`, limites de filiais/usuários), período de validade (`starts_at`, `expires_at`), timestamp de emissão e um `nonce` único (UUID v4).
- O payload é serializado em JSON, codificado em Base64URL e assinado com HMAC-SHA256 utilizando o segredo de infraestrutura `LICENSE_SIGNING_KEY`.
- Durante a ativação em `/license/activate`, o método `LicenseService::activateForTenant()`:
  1. Valida a assinatura digital com `hash_equals()`.
  2. Verifica que `tenant.id` e `tenant.slug` correspondem ao tenant ativo na instalação.
  3. Compara o período com as datas do sistema considerando `clock_skew_minutes` (tolerância de 10 min).
  4. Grava a licença na base de dados local com `LicenseKey::updateOrCreate(['key_hash' => ...])` e ativa o tenant.

$body = base64UrlEncode(json_encode($payload));
$signature = hash_hmac('sha256', $body, config('license.signing_key'));
$token = $body . '.' . $signature;
```
#### Pontos Fortes:
- **Imutabilidade**: O cliente não pode alterar a data de expiração, o nome da empresa ou os módulos sem corromper a assinatura HMAC.
- **Suporte Duplo**: Aceita tanto o token criptográfico longo quanto o serial simplificado de 24 caracteres (`ZBIZ-XXXX-XXXX-XXXX-XXXX`).

A ativação na instalação do cliente (`LicenseService::activateForTenant()`) decodifica o payload, recalcula a assinatura com a `LICENSE_SIGNING_KEY` local e compara usando `hash_equals()` resistente a timing attacks.
#### Vulnerabilidade / Lacuna Identificada:
- **Ausência de Fingerprint de Máquina (Hardware Binding)**:
  - O payload atual vincula a licença estritamente ao `tenant_id` e `slug` da empresa, mas **não vincula à máquina física (Hardware ID, UUID da placa-mãe ou MAC Address)**.
  - Se um cliente técnico copiar a base de dados MySQL local ou duplicar a máquina virtual para 3 filiais distintas da mesma empresa sem autorização, a mesma licença funcionará nas 3 máquinas offline sem acusar duplicidade.

### 1.2. Vulnerabilidade Identificada: Ausência de *Node-Locking* (Fingerprint de Máquina)
- **Problema:** O payload atual vincula a licença estritamente aos metadados do Tenant (`tenant.id`, `tenant.slug`). Contudo, **não existe nenhum identificador físico da máquina servidora**.
- **Impacto Comercial:** Se um cliente copiar a pasta da aplicação (`/var/www/zbiz` ou imagem Docker) ou exportar a base de dados para uma segunda ou terceira máquina, o mesmo token de licença será validado com sucesso em todas elas, permitindo a duplicação não autorizada de instâncias sem pagamento de licença adicional.
#### Recomendação de Engenharia:
1. **Hardware Fingerprint Opcional**:
   - No instalador local (`scripts/install-offline.sh`), recolher o hash da máquina (`sha256(machine-id + cpu-info)`).
   - Opcionalmente, incluir `machine_hash` no payload da licença para instalações de alto valor.
2. **Controlo de Nós no SaaS**:
   - Para instalações online ou híbridas, o comando `zbiz:sync-push` deve enviar o `hostname` e `machine_id` local. Se o servidor central receber pushes com mais de 1 máquina para uma licença de terminal único, notificar o painel do dono.
3. **Ação "Libertar Licença" no Control Center**:
   - Permitir ao Super Admin revogar e emitir uma chave de substituição caso o cliente troque o computador físico da loja.

### 1.3. O Desafio da Chave Serial Legível (`ZBIZ-XXXX-...`) vs. Instalação Desconectada
- O sistema gera simultaneamente uma chave serial legível de 24 caracteres (ex: `ZBIZ-4F92-K81M-Q7P3-9A2E`).
- **Comportamento Atual:** No método `verifyToken()`, a chave serial é pesquisada na tabela local `LicenseKey::where('key_code', ...)->first()`.
- **Gargalo Identificado:** Numa instalação local 100% nova e isolada (que nunca se conectou à nuvem do Owner), a tabela `license_keys` local estará vazia. Portanto, **o cliente não consegue ativar o sistema digitando apenas a chave serial curta**, sendo obrigado a colar o token longo base64 completo (`payload.signature`).
- **Recomendação de Solução:**
  1. **Arquivo de Licença Assinado (.zbizlic):** O Control Center do Dono deve permitir o download de um ficheiro `licenca-[empresa].zbizlic` (contendo o token longo assinado) que pode ser importado via upload simples na tela de ativação, dispensando o operador de copiar e colar centenas de caracteres no terminal ou browser.
  2. **Hardware ID (Node Binding):** 
     - A tela `/license/activate` local passa a exibir um **ID de Hardware** gerado a partir de atributos estáveis do Linux host (ex: `sha256(/etc/machine-id + lshw/cpu/disk)`).
     - O cliente informa este ID ao suporte da Fdsmultiservices via WhatsApp.
     - O Dono embute este `machine_id` no payload da licença.
     - Na ativação local, o `LicenseService` rejeita a licença caso o `machine_id` do hardware não coincida exatamente.

---

## 2. REVOGAÇÃO DE LICENÇA EM CENÁRIOS DESCONECTADOS
### 2.2. Revogação de Licenças em Cenários Offline

### 2.1. Limitação Inerente do Modelo Offline
- No Control Center (`/owner/tenants`), o Super Admin pode clicar em `Revogar Licença`, o que executa:
  ```php
  $license->update(['status' => 'revoked', 'revoked_at' => now()]);
  ```
- **Limitação Real:** Se a instalação do cliente estiver 100% desconectada da Internet (Air-Gapped ou Offline Intencional), a máquina cliente nunca saberá que o Dono revogou a licença no servidor central. O `hash_hmac` continuará matematicamente válido até o campo `expires_at` expirar.
#### Como Funciona Atualmente:
- No Control Center do Dono (`/owner/tenants/{tenant}`), o botão "Revogar Licença" executa `LicenseService::revoke()`, alterando `status = 'revoked'` e gravando `revoked_at = now()`.
- O método `verifyToken()` recusa chaves cujo `status === 'revoked'`.

### 2.2. Recomendações e Medidas de Contorno para a Fdsmultiservices
#### Limitação Técnica Real:
- **O Paradoxo Offline**:
  - Se a instalação da farmácia for **100% offline** (sem internet permanente), a alteração de `status = 'revoked'` feita no servidor Cloud nunca chega à base de dados local do cliente.
  - Como a assinatura HMAC do token já foi gerada e validada, o algoritmo matemático offline continua a considerar a licença válida até à data de `expires_at`.

| Abordagem | Funcionamento | Complexidade | Recomendação |
| :--- | :--- | :--- | :--- |
| **Licenças Curtas (Rolling)** | Emitir licenças offline com validade máxima de 30 a 90 dias, renovadas mediante quitação periódica. | Baixa | **Altamente Recomendada** (Regra de Negócio) |
| **Kill-Token Assinado** | O técnico ou suporte insere uma "Chave de Revogação Assinada" que altera o estado local para `suspended` e bloqueia a base de dados. | Média | Recomendada para suporte presencial/remoto |
| **Heartbeat Sincronizado** | Nas instalações que possuem internet intermitente, o comando de sync (`zbiz:sync-push`) consulta o estado da licença e revoga localmente se cancelado. | Baixa | **Já viabilizada pela Prioridade 4** |
#### Recomendações Práticas para a Equipa Comercial & Suporte:
1. **Emissão de Licenças Curtas (Ciclos de 30 a 90 dias)**:
   - Para clientes offline ou em modalidade de aluguer mensal, **nunca emitir licenças de 1 ou 2 anos**. Emitir licenças de 30 dias (renovadas mensalmente contra pagamento) ou trimestrais.
   - Desta forma, mesmo sem revogação remota, o sistema cessa automaticamente no final do ciclo caso o cliente não pague a renovação.
2. **Ingestão de Revogação via Sync Push**:
   - No fluxo de sincronização assíncrona (`zbiz:sync-push`), o endpoint de resposta da nuvem (`POST /api/sync/ingest`) pode retornar o estado atualizado da licença (`remote_license_status => 'revoked'`). Se presente, a máquina local atualiza o registo local e aciona o bloqueio de segurança.

> [!IMPORTANT]
> **Prática Comercial Recomendada para Moçambique:** Não vender licenças offline "vitalícias" semestrais ou anuais a novos clientes com risco de inadimplência. Recomenda-se a emissão inicial de períodos de 30 dias até a consolidação da relação comercial.

---

## 3. TRATAMENTO DE EXPIRAÇÃO NA INTERFACE (UI) E POS
### 2.3. Gestão de Expiração e Alertas Pré-Aviso na UI

### 3.1. Diagnóstico do Estado Atual
Atualmente, o middleware `CheckSubscriptionStatus.php` adota uma política de bloqueio binário:
```php
if ($tenant->license_expires_at && $tenant->license_expires_at->isPast()) {
    $tenant->update(['license_status' => 'expired']);
    if ($request->isMethodSafe()) {
        session()->flash('warning', 'A licença desta instalação expirou...');
        return $next($request);
    }
    return $this->blocked($request, 'license_expired', 'A licença desta instalação expirou...');
}
```
#### Diagnóstico Atual:
- Em `app/Http/Middleware/CheckSubscriptionStatus.php`, quando `license_expires_at->isPast()`:
  - Métodos de leitura (`GET`) continuam disponíveis em modo somente-leitura com flash de aviso.
  - Métodos de escrita (`POST`, `PUT`, `DELETE`) são bloqueados com redirecionamento para `/license/activate`.
- Em `resources/views/layouts/app.blade.php` (linha 1630), é renderizado o banner vermelho de bloqueio:
  ```blade
  Acesso Operacional Expirado / Suspenso — A licença desta empresa expirou. O sistema está em modo de somente-leitura...
  ```

### 3.2. Riscos Operacionais Identificados
1. **Bloqueio Súbito sem Pré-Aviso:** Um operador de caixa de uma farmácia em atendimento pode ser bloqueado no meio de uma venda no sábado à noite caso o vencimento seja atingido, gerando atrito severo com clientes finais e perda de receitas.
2. **Ausência de Contagem Regressiva na Interface:** O operador e o gerente não recebem alertas prévios de que faltam 15, 7 ou 3 dias para a licença expirar.
#### Lacuna de UX Crítica:
- **Ausência de Alerta Prévio**: O sistema não avisa o operador quando faltam 15, 7 ou 2 dias para a licença terminar.
- O cliente é apanhado de surpresa no dia da expiração durante o atendimento ao público na farmácia, gerando atrito comercial imediato.

### 3.3. Desenho da Solução Proposta (Avisos Escalonados)
#### Solução Proposta para Implementação Futura:
Adicionar em `layouts/app.blade.php` o semáforo de aviso preventivo:
1. **15 a 8 Dias Restantes (Aviso Amarelo/Informativo)**:
   - Banner discreto no topo: *"Aviso de Renovação: A sua licença expira em X dias (DD/MM/AAAA). Contacte a Fdsmultiservices para renovação contínua."*
2. **7 a 1 Dia Restante (Alerta Âmbar/Destaque)**:
   - Card âmbar com botão de atalho direto para o WhatsApp do suporte da Fdsmultiservices.
3. **Expirado (Bloqueio Vermelho)**:
   - Mantém o comportamento atual (somente-leitura com redirecionamento).

```
[ Expiração - 15 Dias ] ──► Banner Azul Informativo no Topbar (Apenas visual)
[ Expiração - 7 Dias ]  ──► Banner Âmbar no Dashboard e POS com link de contacto WhatsApp
[ Expiração - 3 Dias ]  ──► Alerta Vermelho Fixo no Topo do POS com contagem regressiva em horas
[ Expiração - D-Day ]   ──► Carência Operacional de 48 Horas (Emergency Grace Period)
[ D-Day + 48h ]         ──► Bloqueio Total de Escrita (Somente Leitura e Tela de Ativação)
```

#### Regras do Modo Carência Operacional (48 Horas)
- Ao expirar, o sistema entra em `grace_period` durante 48 horas.
- Vendas continuam a ser permitidas no POS, mas um talão impresso e um modal visual alertam:
  *"ATENÇÃO: Sistema em período de tolerância de 48h. Contacte o suporte urgente para renovar."*
- Evita constrangimentos com o público e dá tempo ao proprietário da farmácia para liquidar a fatura.

---

## 4. AUDITORIA E RASTREABILIDADE (TABELA `license_audit_logs`)
### 2.4. Auditoria & Rastreabilidade de Chaves

### 4.1. Lacuna Forense Identificada
Atualmente, a tabela `license_keys` armazena apenas o último estado (`status`, `activated_at`, `revoked_at`). Se uma licença for tentada em múltiplos computadores, compartilhada indevidamente ou sofrer sucessivas tentativas de força bruta na tela de ativação, o sistema não registra rastros históricos.
#### Estado Atual:
- A tabela `license_keys` armazena:
  - `tenant_id`, `plan_id`, `issued_by_user_id`, `key_code`, `key_hash`, `mode`, `status`, `starts_at`, `expires_at`, `activated_at`, `revoked_at`, `payload`, `signature`.
- Isso garante a rastreabilidade básica de quem emitiu a chave no servidor central.

### 4.2. Estrutura Proposta da Tabela `license_audit_logs`
#### Lacuna Identificada:
- Não há registo de **tentativas de ativação com falha** (ex: digitação incorreta, adulteração de assinatura, chaves de outro tenant) nem registo de metadados como endereço IP, navegador ou versão da aplicação cliente.

#### Proposta de Tabela de Auditoria (`license_audit_logs`):
```sql
CREATE TABLE license_audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    license_key_id BIGINT UNSIGNED NULL,
    tenant_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    event VARCHAR(50) NOT NULL, -- 'issued', 'activation_attempt', 'activated', 'revoked', 'expired', 'tamper_detected'
    status VARCHAR(20) NOT NULL, -- 'success', 'failed'
    machine_fingerprint VARCHAR(128) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload_snapshot JSON NULL,
    reason TEXT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (license_key_id) REFERENCES license_keys(id) ON DELETE SET NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
CREATE TABLE `license_audit_logs` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` bigint unsigned NULL,
    `license_key_id` bigint unsigned NULL,
    `event` varchar(50) NOT NULL, -- 'issued', 'activated', 'failed_signature', 'failed_expired', 'revoked'
    `key_code` varchar(50) NULL,
    `ip_address` varchar(45) NULL,
    `user_agent` text NULL,
    `app_version` varchar(20) NULL,
    `details` json NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`license_key_id`) REFERENCES `license_keys`(`id`) ON DELETE SET NULL
);
```

### 4.3. Eventos Críticos a Serem Registados
1. **`ISSUED`**: Registra quando o Dono emitiu a licença no Control Center, quem emitiu e para qual cliente.
2. **`ACTIVATION_SUCCESS`**: Registra a ativação bem-sucedida, coletando fingerprint da máquina e IP.
3. **`ACTIVATION_FAILED`**: Registra qualquer chave incorreta, adulterada ou destinada a outro tenant.
4. **`TAMPER_ATTEMPT`**: Registra quando um cliente tentou modificar manualmente a data no banco ou o payload assinado.

---

## 5. EXPERIÊNCIA DO UTILIZADOR (UX) EM `/license/activate`
### 2.5. Experiência do Utilizador (UX) em `/license/activate`

### 5.1. Avaliação das Mensagens de Erro Atuais
As mensagens de erro atuais atiradas por `LicenseService` são genéricas e puramente técnicas:
- `"Licença inválida ou adulterada."` ➔ Soa acusatório e não orienta o utilizador sobre o que fazer.
- `"Empresa da licença não encontrada nesta instalação."` ➔ Confuso para um cliente leigo.
#### Diagnóstico da Tela Atual:
- O formulário em `resources/views/license/activate.blade.php` e `LicenseActivationController` valida:
  - Se o campo tem pelo menos 19 caracteres (`min:19`).
  - Se a empresa coincide com o token.
  - Se a assinatura ou validade estão corretas.
- As mensagens de erro retornadas pelo `LicenseService` são claras:
  - *"Esta licença foi emitida para outra empresa."*
  - *"Chave serial de licença inválida ou revogada."*
  - *"Licença expirada."*
  - *"Licença ainda não está ativa."*

### 5.2. Catálogo de Mensagens Amigáveis Recomendado
#### Recomendações de Melhoria:
1. **Auto-Formatação do Input Serial**:
   - Inserir máscara visual automática no campo `ZBIZ-____-____-____-____` para evitar que o operador esqueça os hífens ou digite espaços em branco acidentais.
2. **Canal de Apoio no Rodapé**:
   - Incluir botão com link dinâmico para o WhatsApp de suporte:
     `https://wa.me/258862134230?text=Preciso+de+ajuda+com+a+ativacao+da+licenca+da+empresa+X`

| Código Interno | Mensagem Técnica Atual | Nova Mensagem Recomendada ao Cliente |
| :--- | :--- | :--- |
| `ERR_INVALID_SIGNATURE` | Licença inválida ou adulterada. | "O código de licença inserido não é autêntico ou foi digitado incorretamente. Verifique os caracteres e tente novamente." |
| `ERR_EXPIRED` | Licença expirada. | "Esta chave de licença expirou em {data}. Contacte a Fdsmultiservices para renovar o seu plano." |
| `ERR_FUTURE_START` | Licença ainda não está ativa. | "A data do relógio deste computador ({data_atual}) está incorreta. Acerte a hora e o fuso horário da máquina." |
| `ERR_WRONG_TENANT` | Esta licença foi emitida para outra empresa. | "Esta licença foi gerada para a empresa '{tenant_licenca}'. Não é válida para a empresa ativa '{tenant_atual}'." |
| `ERR_WRONG_MACHINE` | (Novo com Hardware Binding) | "Esta licença está vinculada a outro computador servidor. Solicite a migração de máquina ao suporte técnico." |

### 5.3. Melhorias Visuais na Tela de Ativação
1. **Apresentação Clara do Modo de Ativação:**
   - Opção A: Inserção de Chave Serial Online (quando há internet).
   - Opção B: Upload do Ficheiro de Licença Offline (`.zbizlic`).
   - Opção C: Campo amplo para colar o Token Criptográfico com formatação monoespaçada automática.
2. **Cópia Rápida do Machine ID:**
   - Botão "Copiar ID da Máquina" com um clique, facilitando o envio via WhatsApp para o Filipe.

---

## 6. CONCLUSÃO E PLANO DE IMPLEMENTAÇÃO RECOMENDADO
## 3. Matriz de Risco & Recomendações Prioritárias

A auditoria confirma que a fundação criptográfica atual do ZBIZ+ (`HMAC-SHA256`) é sólida, segura e perfeitamente operacional para o cliente atual em produção.
| Área | Nível de Risco | Impacto Operacional | Ação Recomendada |
| :--- | :---: | :--- | :--- |
| **Alertas Pré-Expiração (15/7 dias)** | **Alto** | Clientes apanhados de surpresa com caixas bloqueados. | Implementar banner preventivo âmbar no layout principal. |
| **Prazos de Licenças Offline** | **Médio** | Dificuldade de revogação remota sem internet. | Política comercial de licenças de no máximo 90 dias em modo offline. |
| **Replicação de Instâncias (Clonagem)** | **Médio** | Possibilidade de duplicar VM com mesma licença. | Monitorar instâncias via `sync-push` e planear hardware fingerprint. |
| **Auditoria de Tentativas de Chave** | **Baixo** | Falta de visibilidade de erros de digitação. | Criar migration e model para `license_audit_logs`. |

As melhorias identificadas dividem-se em duas etapas:
---

- **Etapa I (Comercial / Imediata - Sem código novo):**
  - Instruir a equipa comercial a utilizar ciclos de renovação de 30 a 90 dias para instalações offline.
  - Disponibilizar aos clientes o envio do token completo assinado junto ao certificado PDF para ativações locais.
## 4. Conclusão

- **Etapa II (Desenvolvimento Futuro / Após aprovação do Filipe):**
  - Implementação dos avisos escalonados de expiração (15, 7, 3 dias) e carência de 48h.
  - Implementação da migração da tabela `license_audit_logs`.
  - Introdução do Hardware ID (Fingerprint de Máquina) no payload assinado para proteção contra pirataria de software.

---
*Relatório concluído e arquivado sob a referência `docs/14_LICENSE_FLOW_AUDIT.md`.*
O subsistema de licenciamento do ZBIZ+ atinge com êxito os objetivos de segurança, robustez criptográfica e suporte híbrido Cloud/Offline. Com as recomendações operacionais documentadas neste relatório — em especial os avisos preventivos e a disciplina de ciclo curto para máquinas offline —, a Fdsmultiservices dispõe de um modelo de negócio sustentável e escalável para farmácias e retalho em Moçambique.
