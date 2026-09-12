# 📖 Manual do Usuário - ReproSys v2.0.0

**Guia Completo para Usuários**  
FDSMULTSERVICES+ - Sistema de Reprografia e Serigrafia

---

## 📑 Sumário

1. [Começando](#começando)
2. [Autenticação](#autenticação)
3. [Dashboard](#dashboard)
4. [Gestão de Produtos](#gestão-de-produtos)
5. [Gestão de Vendas](#gestão-de-vendas)
6. [Gestão de Estoque](#gestão-de-estoque)
7. [Gestão de Despesas](#gestão-de-despesas)
8. [Gestão de Dívidas](#gestão-de-dívidas)
9. [Relatórios](#relatórios)
10. [Perfil de Usuário](#perfil-de-usuário)
11. [Dicas e Atalhos](#dicas-e-atalhos)

---

## 🎯 Começando

### Requisitos Mínimos

- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- Conexão com a internet
- Acesso credencial ao sistema

### Primeira Vez?

1. Abra o navegador
2. Acesse: `http://seu-servidor/reprosys`
3. Faça login com suas credenciais
4. Explore o dashboard
5. Comece a registrar dados

---

## 🔐 Autenticação

### Login

**Passo 1:** Navegue até a página de login  
**Passo 2:** Insira seu email  
**Passo 3:** Insira sua senha  
**Passo 4:** Clique em "Entrar"

```
┌─────────────────────────────────────┐
│   FDSMULTSERVICES+                  │
│   Sistema de Reprografia Completo   │
├─────────────────────────────────────┤
│ Email: _______________              │
│ Senha: _______________              │
│                                     │
│ ☐ Lembrar-me                       │
│                                     │
│        [    ENTRAR    ]             │
│                                     │
│ Novo funcionário?                   │
│ Solicitar acesso                    │
└─────────────────────────────────────┘
```

### Recuperar Senha

1. Clique em "Esqueceu a senha?"
2. Insira seu email
3. Verifique seu email
4. Clique no link de recuperação
5. Defina uma nova senha
6. Faça login novamente

### Logout

1. Clique no ícone de perfil (canto superior direito)
2. Selecione "Sair"
3. Você será desconectado

---

## 🏠 Dashboard

O Dashboard é a página principal do sistema. Aqui você vê:

### Widgets de Resumo

**Vendas Hoje**
- Número de transações
- Valor total em vendas
- Tendência (↑ ↓)

**Despesas Hoje**
- Quantidade de despesas
- Valor total
- Comparação com período anterior

**Receita Líquida**
- Total de vendas menos despesas
- Percentual de lucro
- Gráfico comparativo

**Devedores**
- Clientes com débitos
- Valor total em aberto
- Quantidade de dívidas

### Gráficos

**Vendas vs. Despesas**
- Comparação mensal
- Análise de tendências
- Projeção

**Produtos Mais Vendidos**
- Top 5 produtos
- Quantidade vendida
- Receita gerada

**Categoria de Despesas**
- Distribuição por tipo
- Percentual de cada categoria

### Ações Rápidas

Botões para ações frequentes:
- ➕ Nova Venda
- ➕ Nova Despesa
- ➕ Novo Produto
- 📊 Ver Relatórios

---

## 📦 Gestão de Produtos

### Visualizar Produtos

**Caminho:** Produtos → Lista de Produtos

A tela mostra:
- ✅ Nome do produto
- 📋 Tipo (Produto/Serviço)
- 💰 Preço
- 📊 Categoria
- ⚙️ Ações (Editar, Deletar)

### Filtrar Produtos

Use os filtros no topo:

```
[Tipo ▼] [Categoria ▼] [Status ▼] [🔍 Buscar...]
```

### Adicionar Novo Produto

**Passo 1:** Clique em "+ Novo Produto"

**Passo 2:** Preencha o formulário

| Campo | Descrição | Exemplo |
|-------|-----------|---------|
| Nome | Nome do produto | Folha A4 Branca |
| Descrição | Detalhe do produto | Papel 75g para cópia |
| Tipo | Produto ou Serviço | ◉ Produto ◯ Serviço |
| Categoria | Selecione categoria | Papel |
| Preço | Valor unitário | 0.50 |
| Estoque Inicial | Quantidade disponível | 1000 |

**Passo 3:** Clique em "Salvar"

### Editar Produto

1. Localize o produto na lista
2. Clique no ícone ✏️ (Editar)
3. Modifique os dados
4. Clique em "Atualizar"

### Deletar Produto

1. Localize o produto
2. Clique no ícone 🗑️ (Deletar)
3. Confirme a exclusão

⚠️ **Atenção:** Produtos com vendas não podem ser deletados

---

## 🛒 Gestão de Vendas

### Criar Nova Venda

**Passo 1:** Acesse Vendas → "+ Nova Venda"

**Passo 2:** Selecione produtos

```
┌─────────────────────────────────────┐
│ PRODUTOS DISPONÍVEIS                │
├─────────────────────────────────────┤
│ [Folha A4]        Preço: 0.50 MT   │
│ Quantidade: [__] Adicionar          │
│                                     │
│ [Papel Ofício]    Preço: 0.45 MT   │
│ Quantidade: [__] Adicionar          │
│                                     │
│ [Envelopes]       Preço: 1.00 MT   │
│ Quantidade: [__] Adicionar          │
└─────────────────────────────────────┘
```

**Passo 3:** Insira quantidades

**Passo 4:** Revise o carrinho

```
┌──────────────────────────────┐
│ CARRINHO                     │
├──────────────────────────────┤
│ Folha A4 x 100 = 50.00 MT   │
│ Papel Ofício x 50 = 22.50 MT│
├──────────────────────────────┤
│ SUBTOTAL:      72.50 MT     │
│ DESCONTO: ___  0.00 MT      │
│ ────────────────────────     │
│ TOTAL:         72.50 MT     │
└──────────────────────────────┘
```

**Passo 5:** Selecione forma de pagamento

- 💳 À Vista
- 📅 À Prazo (defina data)
- 💰 Múltiplas formas

**Passo 6:** Clique em "Concluir Venda"

### Visualizar Histórico de Vendas

**Caminho:** Vendas → Histórico

Veja todas as vendas realizadas com:
- Data e hora
- Produtos vendidos
- Valor total
- Cliente (se registrado)
- Status

### Funções de Vendas

#### Buscar Venda

Use a barra de busca para encontrar vendas por:
- Data
- Valor
- Cliente
- Produto

#### Filtrar Vendas

```
[Data Inicial ▼] [Data Final ▼] 
[Status ▼] [Método de Pagamento ▼]
```

#### Imprimir Comprovante

1. Localize a venda
2. Clique no ícone 🖨️
3. Ajuste preferências de impressão
4. Clique em "Imprimir"

#### Exportar Dados

1. Clique em "Exportar"
2. Escolha formato: PDF ou Excel
3. Defina período
4. Clique em "Baixar"

---

## 📊 Gestão de Estoque

### Visualizar Estoque

**Caminho:** Estoque → Produtos

Veja:
- Produto
- Quantidade em estoque
- Quantidade mínima
- Últimas movimentações

### Entrada de Estoque

**Passo 1:** Clique em "+ Entrada"

**Passo 2:** Preencha dados

| Campo | Descrição |
|-------|-----------|
| Produto | Selecione o produto |
| Quantidade | Quantos itens entram |
| Data | Data da entrada |
| Referência | Nota fiscal, pedido, etc. |
| Observações | Notas adicionais |

**Passo 3:** Clique em "Registrar"

### Saída de Estoque

**Passo 1:** Clique em "+ Saída"

**Passo 2:** Similiar à entrada, mas para saída

### Ajuste de Estoque

Usado para correções de inventário:

1. Clique em "Ajuste"
2. Selecione o produto
3. Insira quantidade atual real
4. Sistema calcula diferença
5. Clique em "Confirmar"

### Alertas de Estoque

Você recebe notificações quando:
- ⚠️ Estoque abaixo do mínimo
- 📉 Produto em falta
- 📈 Estoque próximo do máximo

---

## 💰 Gestão de Despesas

### Registrar Despesa

**Passo 1:** Acesse Despesas → "+ Nova Despesa"

**Passo 2:** Preencha o formulário

| Campo | Descrição | Obrigatório |
|-------|-----------|-------------|
| Descrição | O que foi gasto | ✅ |
| Categoria | Tipo de despesa | ✅ |
| Valor | Quantidade gasto | ✅ |
| Data | Data do gasto | ✅ |
| Recibo Nº | Número do recibo | ❌ |
| Notas | Observações | ❌ |
| Anexo | Foto/PDF do recibo | ❌ |

**Passo 3:** Clique em "Salvar"

### Categorias de Despesas

Exemplos:
- 🏢 Aluguel
- ⚡ Energia
- 💧 Água
- 🚚 Transporte
- 📱 Comunicação
- 🛠️ Manutenção
- 👥 Salários
- 📦 Suprimentos
- 🧹 Limpeza
- 📚 Educação

### Editar Despesa

1. Localize na lista
2. Clique em ✏️
3. Modifique dados
4. Clique em "Atualizar"

### Deletar Despesa

1. Clique em 🗑️
2. Confirme exclusão

### Filtrar Despesas

```
[Período ▼] [Categoria ▼] [Status ▼]
[🔍 Buscar por descrição...]
```

---

## 💳 Gestão de Dívidas

### Registrar Dívida

**Passo 1:** Acesse Dívidas → "+ Nova Dívida"

**Passo 2:** Preencha formulário

| Campo | Descrição |
|-------|-----------|
| Cliente | Nome do devedor |
| Telefone | Contato |
| Documento | ID/Bilhete |
| Valor Original | Débito inicial |
| Data | Quando começou |
| Data Vencimento | Prazo de pagamento |
| Descrição | Motivo da dívida |

**Passo 3:** Clique em "Registrar"

### Status da Dívida

- 🟢 **Ativa** - Débito pendente
- 🟡 **Parcial** - Pagamento parcial
- 🟢 **Paga** - Débito quitado
- 🔴 **Vencida** - Prazo expirado
- ⚫ **Cancelada** - Não vai ser cobrada

### Registrar Pagamento

1. Clique na dívida
2. Clique em "+ Registrar Pagamento"
3. Insira valor pago
4. Selecione forma de pagamento
5. Clique em "Confirmar"

### Visualizar Dívidas

**Caminho:** Dívidas → Lista

Veja:
- Cliente
- Valor original
- Valor pago
- Saldo devedor
- Status
- Data vencimento

### Filtros e Busca

```
[Status ▼] [Ordenar por ▼]
[🔍 Buscar cliente...]
```

### Relatório de Dívidas

1. Clique em "Relatório"
2. Escolha período
3. Veja gráficos e estatísticas
4. Exporte em PDF/Excel

---

## 📊 Relatórios

### Dashboard de Relatórios

Acesse: Relatórios → Dashboard

Veja visão geral com:
- Total de vendas
- Total de despesas
- Lucro/Prejuízo
- Dívidas pendentes
- Estoque total

### Fluxo de Caixa

**Passo 1:** Clique em "Fluxo de Caixa"

**Passo 2:** Selecione período

```
[Data Inicial] → [Data Final]
```

**Passo 3:** Visualize gráficos

**Gráfico 1: Entradas vs Saídas**
- Linha com tendências
- Valores diários/mensais
- Comparação

**Gráfico 2: Composição de Receita**
- Quanto vem de cada produto
- Percentual por categoria

**Gráfico 3: Distribuição de Despesas**
- Por categoria
- Valor e percentual

**Passo 4:** Exporte dados

```
[📄 PDF] [📊 Excel] [📋 CSV]
```

### Relatório de Vendas

**Inclui:**
- Quantidade de vendas
- Receita total
- Produtos mais vendidos
- Cliente de maior valor
- Ticket médio

### Relatório de Produtos

**Mostra:**
- Produtos mais movimentados
- Estoque atual
- Valor em estoque
- Giro de estoque

### Relatório de Dívidas

**Contém:**
- Total em dívida
- Dívidas por cliente
- Dívidas vencidas
- Histórico de pagamentos

---

## 👤 Perfil de Usuário

### Acessar Perfil

1. Clique no ícone 👤 (canto superior direito)
2. Selecione "Meu Perfil"

### Editar Perfil

**Informações Pessoais:**
- Nome completo
- Email
- Telefone
- Foto de perfil

**Segurança:**
- Alterar senha
- Autenticação em dois fatores

### Alterar Senha

1. Clique em "Segurança"
2. Clique em "Alterar Senha"
3. Insira senha atual
4. Insira nova senha
5. Confirme nova senha
6. Clique em "Atualizar"

**Requisitos de senha:**
- ✅ Mínimo 8 caracteres
- ✅ Pelo menos 1 letra maiúscula
- ✅ Pelo menos 1 número
- ✅ Pelo menos 1 caractere especial

---

## ⚙️ Configurações

### Acessar Configurações

Caminho: Configurações → Geral

### Opções Disponíveis

**Empresa:**
- Nome
- Logo
- Telefone
- Email
- Endereço

**Localização:**
- Moeda (MT, €, $)
- Fuso horário
- Idioma

**Formato:**
- Idioma da interface
- Formato de data
- Formato numérico

**Notificações:**
- ☑️ Vendas abaixo de meta
- ☑️ Estoque baixo
- ☑️ Dívidas vencidas
- ☑️ Despesas altas

---

## 💡 Dicas e Atalhos

### Atalhos de Teclado


Esta secção aplica-se apenas a empresas configuradas com o setor **Restaurante & Bar**.

| Tecla | Ação |
|-------|------|
| `Ctrl+K` | Abrir busca global |
| `Ctrl+Shift+T` | Alternar tema |
| `Ctrl+,` | Configurações |
| `Alt+E` | Escapar/Fechar modal |
| `Tab` | Navegar entre campos |
| `Enter` | Confirmar |

### Dicas Úteis

1. **Busca Rápida:** Use `Ctrl+K` para buscar qualquer coisa no sistema

2. **Tema Escuro:** Clique no ícone de tema para alternar entre claro/escuro

3. **Export Automático:** Configure exportações agendadas nos relatórios

4. **Backup:** Realiza backup automático diariamente às 00:00

5. **Modo Offline:** Os dados são sincronizados quando reconectar

6. **Impressão:** Use `Ctrl+P` para imprimir qualquer página

7. **Voltar:** Use `Alt+Seta Esquerda` ou clique no botão "Voltar"

### Boas Práticas

✅ **Faça:**
- Registre vendas no mesmo dia
- Revise estoque semanalmente
- Gere relatórios mensais
- Altere senha regularmente
- Faça backup dos dados
- Use categorias corretas

❌ **Não faça:**
- Compartilhe suas credenciais
- Delete dados sem confirmar
- Ignore alertas de estoque
- Deixe dívidas sem registrar
- Use a conta de admin para tudo

---

## 🆘 Suporte e Ajuda

### Documentação Técnica

- 📖 [README do Projeto](README.md)
- 🔧 [Guia de Instalação](INSTALL.md)

### Contato para Suporte

- **Email:** filipeive@example.com
- **Telefone:** +258 87 XXX XXXX
- **Portal:** http://163.192.7.41/
- **Chat:** Disponível no sistema

### Reportar Problemas

1. Descreva o problema
2. Inclua passos para reproduzir
3. Captura de tela se possível
4. Versão do navegador
5. Envie ao suporte

---

## 📝 Changelog

### Versão 2.0.0 (Novembro 2025)
- ✨ Nova interface com Tailwind CSS
- 📊 Dashboard melhorado
- 🔍 Busca avançada
- 📱 Responsivo para mobile
- 🔐 Melhorias de segurança
- ⚡ Melhor performance

---

## 📄 Termos de Uso

Este sistema é propriedade de **FDSMULTSERVICES+** e desenvolvido por **Eng. Filipe dos Santos**.

Você concorda em:
- ✅ Usar apenas para fins legítimos
- ✅ Não compartilhar credenciais
- ✅ Manter dados confidenciais
- ✅ Reportar vulnerabilidades
- ✅ Cumprir licença MIT

---

**Versão do Manual:** 2.0.0  
**Última Atualização:** Novembro de 2025  
**Desenvolvido por:** Eng. Filipe dos Santos  
**FDSMULTSERVICES+**

---

### 🎓 Precisa de Ajuda?

- 📞 Ligue para suporte
- 📧 Envie um email
- 💬 Acesse o chat online
- 🌐 Visite o portal web

**Estamos aqui para ajudar!** 
