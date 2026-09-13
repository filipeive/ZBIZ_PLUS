# ROADMAP DE DESENVOLVIMENTO: ZBIZ+
## `docs/03_ROADMAP.md`

---

## 1. ESTADO ATUAL DO PROJETO (VERSÃO 1.0.18)

O **ZBIZ+** evoluiu de um sistema de reprografia monoposto (antigo ReproSys) para um **ERP & POS SaaS Multi-Tenant e Multissetorial** moderno, robusto e em produção para o mercado de Moçambique.

```text
[CONCLUÍDO] FASE 0: Auditoria & Estratégia de Arquitetura Monolítica Modular
[CONCLUÍDO] FASE 1: Setup do Repositório ZBIZ_PLUS & Documentação Técnica Base
[CONCLUÍDO] FASE 2: Fundação Multi-Tenancy & Isolamento Seguro (BelongsToTenant, TenantScope, RLS)
[CONCLUÍDO] FASE 3: ZBIZ Core & Multi-Branch (Empresas, Filiais, Armazéns, Caixa, Ledger Imutável)
[CONCLUÍDO] FASE 4: SaaS Owner Control Center (MRR/ARR, Chaves Seriais ZBIZ, Certificados PDF, Impersonate)
[CONCLUÍDO] FASE 5: ZBIZ POS 2.0 (Interface Rápida, Suporte a Scanner de Barras, Descontos Automáticos/Manuais)
[CONCLUÍDO] FASE 6: Vertical 1 — Retalho Geral & Serviços/Reprografia (Migração Completa do ReproSys)
[CONCLUÍDO] FASE 7: Vertical 2 — ZBIZ Pharmacy (Lotes, Validades 30/60/90d, Saída FEFO, Regulatório ANARME)
[CONCLUÍDO] FASE 8: Vertical 3 — Restaurante & F&B (Mapa de Mesas, Estados de Sala, Comandas)
[CONCLUÍDO] FASE 9: Catálogo Híbrido Universal (Venda de Produtos Físicos e Serviços Médicos/Técnicos)
[EM EVOLUÇÃO] FASE 10: Certificação Fiscal Avançada (Séries Fiscais AT Moçambique, Assinatura RSA em Licenças)
```

---

## 2. HISTÓRICO DE MARCOS DE ENTREGA (MILESTONES)

* **M1 (v0.1.0):** Fundação Multi-Tenant com isolamento estrito verificado via testes automatizados.
* **M2 (v0.2.0):** Multi-Filiais com armazéns separados, gestão de caixa centralizada e transações imutáveis no Livro-Razão.
* **M3 (v0.3.0):** POS 2.0 com suporte a leitor de código de barras, atalhos de teclado e recibos térmicos.
* **M4 (v0.4.0):** Módulo de Farmácia com gestão de lotes, datas de expiração e alertas automáticos ANARME.
* **M5 (v1.0.0):** Lançamento Comercial SaaS, motor de subscrições, pagamentos manuais e suporte a M-Pesa / e-Mola.
* **M6 (v1.0.16):** Flexibilização universal do catálogo — Farmácias e empresas de qualquer ramo agora podem registar e vender Serviços (consultas, injeções, medições de tensão, encadernações).
* **M7 (v1.0.17):** Novo Control Center para o Dono (Owner), isolando o menu SaaS de operações de tenant, padronização de chaves de licença seriais (`ZBIZ-XXXX-XXXX-XXXX-XXXX`) e correção de temas Dark/Light.
* **M8 (v1.0.18):** Certificados Oficiais de Licença de Software com assinatura HMAC SHA-256 e exportação em PDF, Modo Suporte Técnico (Impersonate) com barra persistente, Onboarding Rápido de Empresas e Métricas MRR/ARR.

---

## 3. PRÓXIMAS ETAPAS (Q4 2026)

1. **Módulo KDS (Kitchen Display System) para Restaurantes:** Telas em tempo real para despache de pedidos na cozinha e bar.
2. **Sincronização Híbrida Offline-Online (Edge Sync):** Sincronização em background quando termina o corte de energia/internet local.
3. **App Mobile PWA para Gestores:** Dashboards operacionais rápidos para acompanhamento no smartphone.

---

────────────────────────────────────────────────────────────────────────────  
**Desenvolvido por: fdev-ms (FDS Multiservices)**  
*Engenharia de Software & Suporte Técnico:* `fdev-ms@fdevms:~/Filipe/reprosys$`  
*Contacto & Assistência:* (+258) 84 999 1122 · Quelimane / Moçambique  
*Plataforma ZBIZ+ Enterprise Cloud & POS Suite — Versão 1.0.18*  
────────────────────────────────────────────────────────────────────────────  
