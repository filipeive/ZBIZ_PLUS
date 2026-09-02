# Owner Control Center e Licenças Offline

## Objetivo

O ZBIZ+ deve suportar dois modelos comerciais:

- **SaaS/Cloud**: o cliente usa a instalação central, com subscrição validada diretamente no servidor.
- **Instalação local/offline**: o cliente instala o sistema na própria máquina ou rede local, e ativa a aplicação com uma licença assinada emitida pelo dono do sistema.

Esta separação evita misturar gestão comercial com operação do tenant. O cliente gere vendas, stock, finanças e equipa; o dono do sistema gere clientes, planos, módulos, limites e validade.

## Componentes

### Control Center do Dono

Área restrita a utilizadores `super_admin`, disponível em:

```text
/owner/tenants
```

Responsabilidades:

- listar e pesquisar tenants/clientes;
- ver estado, plano, modo de instalação e validade;
- atualizar dados comerciais do tenant;
- trocar plano e estado da subscrição;
- definir modo `cloud`, `local_online` ou `offline`;
- emitir e revogar chaves de licença.

### Subscrição

A tabela `subscriptions` continua a representar o ciclo comercial online:

- plano contratado;
- estado da subscrição;
- período atual;
- método/referência de pagamento.

O middleware `subscription` bloqueia escrita quando a subscrição ou licença expira, mantendo leitura em modo somente-leitura.

### Licença de Software & Chave Serial

A tabela `license_keys` guarda o histórico das chaves emitidas e o código legível da licença:

- `key_code`: Código serial amigável no formato padrão de software `ZBIZ-XXXX-XXXX-XXXX-XXXX` (ex: `ZBIZ-4F92-K81M-Q7P3-9A2E`);
- `key_hash`: Hash SHA-256 da chave / token;
- `tenant_id`: Empresa / Tenant destinatário;
- `plan_id`: Plano contratado;
- `mode`: Modo de instalação (`cloud`, `local_online`, `offline`);
- `validade`: Datas de início e expiração (`starts_at` e `expires_at`);
- `signature`: Assinatura digital HMAC para validação criptográfica offline;
- `status`: `issued`, `active`, `revoked`, `expired`.

A ativação do software suporta dois formatos transparentes:
1. **Chave Serial Humana**: `ZBIZ-XXXX-XXXX-XXXX-XXXX` (fácil de digitar ou copiar para o cliente);
2. **Certificado Assinado Completo**: `base64url(payload).assinatura_hmac` para instalações em modo air-gapped rigoroso sem acesso à rede.

## Fluxo Cloud

1. Dono cria ou aprova tenant.
2. Define plano e validade no Control Center.
3. Sistema atualiza a subscrição do tenant.
4. O tenant usa apenas os módulos permitidos pelo plano.

## Fluxo Offline

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

## Segurança

- A licença não deve ser editável manualmente pelo cliente.
- `LICENSE_SIGNING_KEY` deve ser diferente do `APP_KEY` em produção.
- A chave privada/segredo deve ficar apenas com o dono do sistema.
- Revogar licença funciona imediatamente no modo cloud/local-online; em instalações totalmente offline, a revogação só é aplicada quando houver nova ativação, atualização manual ou sincronização.

## Configuração

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
