# Backlog Futuro: Regularização de Turnos e Métricas Financeiras

## 1. Fecho Administrativo de Turno Antigo

### Contexto

Um operador pode deixar um turno aberto de um dia anterior. O sistema não deve abrir outro turno automaticamente nem encerrá-lo com um saldo presumido, porque isso comprometeria a auditoria do caixa.

### Solução futura

Criar uma ação separada do fecho normal do operador, disponível apenas para administrador ou supervisor autorizado:

1. Listar turnos antigos ainda abertos.
2. Selecionar o turno a regularizar.
3. Mostrar o operador original, filial, data de abertura e vendas associadas.
4. Exigir a contagem física real da gaveta.
5. Exigir uma justificação administrativa.
6. Registar o utilizador que fez a regularização.
7. Calcular e guardar a quebra ou sobra.
8. Emitir o Talão Fecho Z e o Relatório de Fecho A4.

### Requisitos de auditoria

- Adicionar, se necessário, `closed_by_user_id` ao turno.
- Guardar o tipo de fecho: `operator` ou `administrative`.
- Não alterar o operador original do turno.
- Não permitir regularização sem contagem física e justificação.
- Registar atividade administrativa no histórico de auditoria.
- Pedir confirmação explícita antes do encerramento.

## 2. Análise dos Cards de Saldo

### Estado da recomendação

A primeira etapa foi implementada: o Dashboard passou a mostrar **Valor Real do Negócio**, enquanto Finanças mantém a métrica detalhada como **Liquidez Atual** e **A Receber**. A resolução de contas do recebimento de vendas também passou a respeitar tenant e filial.

### Situação atual

O Dashboard e a página de Finanças exibem o mesmo valor:

- Dashboard: `Saldo em Caixa (Real)`.
- Finanças: `Saldo Real em Caixa`.
- Ambos usam `currentCapital`.
- `currentCapital` é calculado pela soma dos `current_balance` das contas financeiras operacionais ativas.

Logo, os dois cards são redundantes. O valor não representa apenas dinheiro físico: pode incluir caixa principal, carteira móvel e outras contas operacionais.

### Recomendação de produto

Manter a métrica detalhada em **Finanças**, com nomenclatura mais precisa:

- **Liquidez Atual** ou **Saldo das Contas Operacionais**.
- Descrição: "Soma atual de caixa, carteiras e contas operacionais".

No Dashboard, substituir o card duplicado por um indicador consolidado e mais relevante:

- **Valor Real do Negócio** = Capital em Caixa + A Receber.

Alternativas possíveis:

- Turnos de caixa abertos.
- Despesas de hoje.
- Vendas recebidas hoje.

A escolha recomendada é Valor Real do Negócio, porque dá uma visão imediata da posição económica da empresa sem apresentar valores a receber como se fossem dinheiro disponível.

### Atualização e consistência

O saldo das contas só muda quando uma entrada ou saída é registada no Livro-Razão. Vendas a crédito não aumentam liquidez até serem recebidas, enquanto vendas pagas devem criar uma transação financeira.

Durante uma implementação de melhoria, validar:

- Cada venda recebida gera uma única transação `sale_receipt`.
- Cada despesa gera uma saída no Livro-Razão.
- Reversões não continuam incluídas nos totais.
- O filtro de filial é respeitado.
- A conta financeira resolvida pertence ao tenant e à filial corretos.
- O cache HTTP não impede a atualização dos cards.

### Risco técnico identificado

O método `FinancialLedgerService::syncSale()` procura a conta financeira por `slug` sem restringir inicialmente por `tenant_id` e `branch_id`. Em ambientes multi-tenant ou multi-filial, isso pode associar uma venda à conta de outra empresa/filial se existirem slugs iguais.

Antes de confiar no card como saldo operacional, a resolução da conta deve ser limitada ao tenant atual e, quando existir, à filial da venda, com fallback controlado para a conta operacional correta.

## 3. Decisão proposta

- Não manter dois cards com o mesmo valor.
- Preservar Liquidez Atual em Finanças.
- Manter o card Valor Real do Negócio no Dashboard, com a descrição "Capital em caixa + valores a receber".
- Corrigir e testar a resolução de contas do ledger antes de considerar o saldo financeiro totalmente confiável.
- Implementar o Fecho Administrativo como funcionalidade futura separada.
