# ARQUITETURA MULTI-TENANT: ZBIZ+
## `docs/05_MULTI_TENANCY.md`

---

## 1. DECISÃO: SINGLE DATABASE COM `tenant_id`
Optou-se pelo modelo de banco de dados único com isolamento lógico estrito por coluna `tenant_id`.

### Vantagens:
* Custo mínimo de infraestrutura (executa centenas de tenants num servidor acessível).
* Manutenção simplificada (migrações executadas de forma centralizada).
* Facilidade para cópias de segurança automatizadas.

## 2. MECANISMO DE ISOLAMENTO
1. **Trait `BelongsToTenant`:**
   * Aplica automaticamente o `addGlobalScope` com `where('tenant_id', current_tenant_id())`.
   * Preenche automaticamente o `tenant_id` no hook `creating`.
2. **Middleware `IdentifyTenant`:**
   * Identifica o tenant ativo através do subdomínio (ex: `empresa.zbizplus.co.mz`), sessão ou chave de API.
3. **Testes de Vazamento de Dados (*Tenant Isolation Tests*):**
   * Cobertura de testes garantindo que requisições forçadas com IDs de outros tenants retornem 404.
