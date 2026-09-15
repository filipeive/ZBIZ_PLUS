# Auditoria Técnica: Fluxo de Licenciamento Ponta-a-Ponta (SaaS & Offline)
## Documento Oficial: `docs/14_LICENSE_FLOW_AUDIT.md`
**Data da Auditoria:** 15 de Setembro de 2026  
**Sistema:** ZBIZ+ (Enterprise Multi-Tenant SaaS & On-Premises)  
**Versão Auditada:** v1.0.21  
**Autor:** Engenharia de Plataforma ZBIZ+ / Fdsmultiservices  

---

## 1. Sumário Executivo

O **ZBIZ+** foi concebido para atender à realidade de Moçambique e mercados emergentes, suportando dois modos de operação:
1. **Cloud SaaS**: Aplicação centralizada em nuvem com subscrições gerenciadas online.
2. **On-Premises / Offline**: Instalação dedicada no cliente (ex: farmácias com conectividade instável), funcionando autonomamente com chaves seriais legíveis (`ZBIZ-XXXX-XXXX-XXXX-XXXX`) e tokens criptográficos assinados via HMAC-SHA256.

Esta auditoria técnica avaliou o ciclo de vida completo das licenças: **Emissão no Control Center ➔ Ativação pelo Cliente ➔ Validação Criptográfica ➔ Enforcing de Validade ➔ Revogação ➔ Auditoria & Rastreabilidade**.

O resultado demonstra que a arquitetura criptográfica é sólida e matematicamente inviolável sem a `LICENSE_SIGNING_KEY`, mas identifica oportunidades críticas de melhoria na experiência do utilizador (alertas prévios de expiração) e no controlo contra replicação de nós em máquinas estritamente offline.

---

## 2. Análise Detalhada dos 5 Pilares de Licenciamento

### 2.1. Vínculo de Ativação & Anti-Pirataria (`LicenseService::activateForTenant`)

#### Como Funciona Atualmente:
- No Control Center (`/owner/tenants`), o Super Admin emite uma licença através de `LicenseService::issue()`.
- O payload inclui os dados do tenant (`id`, `slug`, `name`, `business_type`), plano (`features`, limites de filiais/usuários), período de validade (`starts_at`, `expires_at`), timestamp de emissão e um `nonce` único (UUID v4).
- O payload é serializado em JSON, codificado em Base64URL e assinado com HMAC-SHA256 utilizando o segredo de infraestrutura `LICENSE_SIGNING_KEY`.
- Durante a ativação em `/license/activate`, o método `LicenseService::activateForTenant()`:
  1. Valida a assinatura digital com `hash_equals()`.
  2. Verifica que `tenant.id` e `tenant.slug` correspondem ao tenant ativo na instalação.
  3. Compara o período com as datas do sistema considerando `clock_skew_minutes` (tolerância de 10 min).
  4. Grava a licença na base de dados local com `LicenseKey::updateOrCreate(['key_hash' => ...])` e ativa o tenant.

#### Pontos Fortes:
- **Imutabilidade**: O cliente não pode alterar a data de expiração, o nome da empresa ou os módulos sem corromper a assinatura HMAC.
- **Suporte Duplo**: Aceita tanto o token criptográfico longo quanto o serial simplificado de 24 caracteres (`ZBIZ-XXXX-XXXX-XXXX-XXXX`).

#### Vulnerabilidade / Lacuna Identificada:
- **Ausência de Fingerprint de Máquina (Hardware Binding)**:
  - O payload atual vincula a licença estritamente ao `tenant_id` e `slug` da empresa, mas **não vincula à máquina física (Hardware ID, UUID da placa-mãe ou MAC Address)**.
  - Se um cliente técnico copiar a base de dados MySQL local ou duplicar a máquina virtual para 3 filiais distintas da mesma empresa sem autorização, a mesma licença funcionará nas 3 máquinas offline sem acusar duplicidade.

#### Recomendação de Engenharia:
1. **Hardware Fingerprint Opcional**:
   - No instalador local (`scripts/install-offline.sh`), recolher o hash da máquina (`sha256(machine-id + cpu-info)`).
   - Opcionalmente, incluir `machine_hash` no payload da licença para instalações de alto valor.
2. **Controlo de Nós no SaaS**:
   - Para instalações online ou híbridas, o comando `zbiz:sync-push` deve enviar o `hostname` e `machine_id` local. Se o servidor central receber pushes com mais de 1 máquina para uma licença de terminal único, notificar o painel do dono.
3. **Ação "Libertar Licença" no Control Center**:
   - Permitir ao Super Admin revogar e emitir uma chave de substituição caso o cliente troque o computador físico da loja.

---

### 2.2. Revogação de Licenças em Cenários Offline

#### Como Funciona Atualmente:
- No Control Center do Dono (`/owner/tenants/{tenant}`), o botão "Revogar Licença" executa `LicenseService::revoke()`, alterando `status = 'revoked'` e gravando `revoked_at = now()`.
- O método `verifyToken()` recusa chaves cujo `status === 'revoked'`.

#### Limitação Técnica Real:
- **O Paradoxo Offline**:
  - Se a instalação da farmácia for **100% offline** (sem internet permanente), a alteração de `status = 'revoked'` feita no servidor Cloud nunca chega à base de dados local do cliente.
  - Como a assinatura HMAC do token já foi gerada e validada, o algoritmo matemático offline continua a considerar a licença válida até à data de `expires_at`.

#### Recomendações Práticas para a Equipa Comercial & Suporte:
1. **Emissão de Licenças Curtas (Ciclos de 30 a 90 dias)**:
   - Para clientes offline ou em modalidade de aluguer mensal, **nunca emitir licenças de 1 ou 2 anos**. Emitir licenças de 30 dias (renovadas mensalmente contra pagamento) ou trimestrais.
   - Desta forma, mesmo sem revogação remota, o sistema cessa automaticamente no final do ciclo caso o cliente não pague a renovação.
2. **Ingestão de Revogação via Sync Push**:
   - No fluxo de sincronização assíncrona (`zbiz:sync-push`), o endpoint de resposta da nuvem (`POST /api/sync/ingest`) pode retornar o estado atualizado da licença (`remote_license_status => 'revoked'`). Se presente, a máquina local atualiza o registo local e aciona o bloqueio de segurança.

---

### 2.3. Gestão de Expiração e Alertas Pré-Aviso na UI

#### Diagnóstico Atual:
- Em `app/Http/Middleware/CheckSubscriptionStatus.php`, quando `license_expires_at->isPast()`:
  - Métodos de leitura (`GET`) continuam disponíveis em modo somente-leitura com flash de aviso.
  - Métodos de escrita (`POST`, `PUT`, `DELETE`) são bloqueados com redirecionamento para `/license/activate`.
- Em `resources/views/layouts/app.blade.php` (linha 1630), é renderizado o banner vermelho de bloqueio:
  ```blade
  Acesso Operacional Expirado / Suspenso — A licença desta empresa expirou. O sistema está em modo de somente-leitura...
  ```

#### Lacuna de UX Crítica:
- **Ausência de Alerta Prévio**: O sistema não avisa o operador quando faltam 15, 7 ou 2 dias para a licença terminar.
- O cliente é apanhado de surpresa no dia da expiração durante o atendimento ao público na farmácia, gerando atrito comercial imediato.

#### Solução Proposta para Implementação Futura:
Adicionar em `layouts/app.blade.php` o semáforo de aviso preventivo:
1. **15 a 8 Dias Restantes (Aviso Amarelo/Informativo)**:
   - Banner discreto no topo: *"Aviso de Renovação: A sua licença expira em X dias (DD/MM/AAAA). Contacte a Fdsmultiservices para renovação contínua."*
2. **7 a 1 Dia Restante (Alerta Âmbar/Destaque)**:
   - Card âmbar com botão de atalho direto para o WhatsApp do suporte da Fdsmultiservices.
3. **Expirado (Bloqueio Vermelho)**:
   - Mantém o comportamento atual (somente-leitura com redirecionamento).

---

### 2.4. Auditoria & Rastreabilidade de Chaves

#### Estado Atual:
- A tabela `license_keys` armazena:
  - `tenant_id`, `plan_id`, `issued_by_user_id`, `key_code`, `key_hash`, `mode`, `status`, `starts_at`, `expires_at`, `activated_at`, `revoked_at`, `payload`, `signature`.
- Isso garante a rastreabilidade básica de quem emitiu a chave no servidor central.

#### Lacuna Identificada:
- Não há registo de **tentativas de ativação com falha** (ex: digitação incorreta, adulteração de assinatura, chaves de outro tenant) nem registo de metadados como endereço IP, navegador ou versão da aplicação cliente.

#### Proposta de Tabela de Auditoria (`license_audit_logs`):
```sql
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

---

### 2.5. Experiência do Utilizador (UX) em `/license/activate`

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

#### Recomendações de Melhoria:
1. **Auto-Formatação do Input Serial**:
   - Inserir máscara visual automática no campo `ZBIZ-____-____-____-____` para evitar que o operador esqueça os hífens ou digite espaços em branco acidentais.
2. **Canal de Apoio no Rodapé**:
   - Incluir botão com link dinâmico para o WhatsApp de suporte:
     `https://wa.me/258862134230?text=Preciso+de+ajuda+com+a+ativacao+da+licenca+da+empresa+X`

---

## 3. Matriz de Risco & Recomendações Prioritárias

| Área | Nível de Risco | Impacto Operacional | Ação Recomendada |
| :--- | :---: | :--- | :--- |
| **Alertas Pré-Expiração (15/7 dias)** | **Alto** | Clientes apanhados de surpresa com caixas bloqueados. | Implementar banner preventivo âmbar no layout principal. |
| **Prazos de Licenças Offline** | **Médio** | Dificuldade de revogação remota sem internet. | Política comercial de licenças de no máximo 90 dias em modo offline. |
| **Replicação de Instâncias (Clonagem)** | **Médio** | Possibilidade de duplicar VM com mesma licença. | Monitorar instâncias via `sync-push` e planear hardware fingerprint. |
| **Auditoria de Tentativas de Chave** | **Baixo** | Falta de visibilidade de erros de digitação. | Criar migration e model para `license_audit_logs`. |

---

## 4. Conclusão

O subsistema de licenciamento do ZBIZ+ atinge com êxito os objetivos de segurança, robustez criptográfica e suporte híbrido Cloud/Offline. Com as recomendações operacionais documentadas neste relatório — em especial os avisos preventivos e a disciplina de ciclo curto para máquinas offline —, a Fdsmultiservices dispõe de um modelo de negócio sustentável e escalável para farmácias e retalho em Moçambique.
