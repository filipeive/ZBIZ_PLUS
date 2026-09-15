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

## 3. CONTROLO DE TURNO ANTES DA VENDA
* O operador não administrador deve possuir um turno de caixa aberto na data atual para finalizar uma venda.
* O botão de finalização e o atalho F9 abrem o modal de abertura quando não existe turno válido.
* O endpoint de venda repete a validação no servidor, incluindo vendas offline sincronizadas.
* Administradores podem registar vendas sem turno; quando possuem um turno aberto, as vendas continuam vinculadas a ele.
* Um turno aberto de data anterior não é reutilizado como turno do dia atual.

## 4. FECHO E COMPROVATIVOS DO TURNO
* O fecho normal é cego: o operador informa a contagem física sem ver previamente o saldo esperado.
* Depois do fecho, o POS disponibiliza o Talão Fecho Z térmico e o Relatório de Fecho A4.
* O Relatório A4 contém identificação da empresa/filial, operador, período do turno, totais por método de pagamento, auditoria de numerário, diferença, observações e assinaturas.
* Turnos antigos devem ser resolvidos por fecho administrativo, com contagem física e justificação obrigatórias. O sistema não deve fechar turnos automaticamente com saldo presumido.
