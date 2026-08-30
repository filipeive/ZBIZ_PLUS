# ESPECIFICAÇÃO DO ZBIZ POS 2.0
## `docs/08_POS_SPECIFICATION.md`

---

## 1. PRINCÍPIOS DE UX DO POS
* **Velocidade de Caixa:** Todo o fluxo de venda pode ser operado via teclado numérico ou ecrã táctil.
* **Atalhos Rápidos:** F2 (Pesquisa rápida), F4 (Desconto), F8 (Finalizar com M-Pesa), F9 (Finalizar com Numerário).
* **Impressão Térmica:** Suporte a talões de 80mm e 58mm via ESC/POS e impressão direta no navegador.

## 2. ARQUITETURA OFFLINE-FIRST
* Se a conexão cair, o POS grava a venda no **IndexedDB** local e emite o talão com aviso "Pendente de Sincronização".
* Ao restabelecer a conexão, o Service Worker dispara a sincronização idempotente com o servidor central.
