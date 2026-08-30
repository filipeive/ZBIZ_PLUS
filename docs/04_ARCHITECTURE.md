# ARQUITETURA DE SOFTWARE: ZBIZ+
## `docs/04_ARCHITECTURE.md`

---

## 1. PADRÃO ARQUITETURAL
O ZBIZ+ adota o padrão **Modular Monolith** estruturado em camadas limpas:

```text
[ HTTP / API / Web ] ──► [ Middleware (Tenant, Auth, Permissions) ]
                               │
                               ▼
                    [ Form Requests (Validação) ]
                               │
                               ▼
                    [ Domain Actions / Services ]
                               │
                               ▼
                 [ Eloquent Models (BelongsToTenant) ]
                               │
                               ▼
                 [ MySQL Database (Single Database) ]
```

## 2. COMPONENTES CENTRAIS
1. **`FinancialLedgerService`:** Processamento atómico de lançamentos com locks pessimistas.
2. **`InventoryManagerService`:** Controlo de stock por filial e vinculação de insumos.
3. **`PharmacyBatchService`:** Seleção automática de saída de lotes por FEFO (*First Expired, First Out*).
4. **`DiscountEngineService`:** Rateio proporcional de descontos gerais sobre itens.

## 3. POLÍTICA DE DEPENDÊNCIAS
* Laravel 12 Framework
* Spatie Laravel Permission
* Barryvdh Laravel DomPDF
* Maatwebsite Excel
