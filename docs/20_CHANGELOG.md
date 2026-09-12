# CHANGELOG: ZBIZ+
## `docs/20_CHANGELOG.md`

Todas as alterações notáveis no projeto ZBIZ+ serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/), e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

---

## [Unreleased]
### Added
- **Alertas automáticos de validade por filial:** o dashboard consulta lotes vencidos e próximos do vencimento (90 dias) da filial ativa, atualiza a cada 30 segundos, exibe estados de carregamento/erro e oferece som configurável com Web Audio API.
- **Consultas de produto entre filiais:** adicionado fluxo protegido por tenant para solicitar disponibilidade de produto a outra filial, responder, cancelar e acompanhar o estado da consulta.
- **Proteção reforçada do Owner:** rotas `/owner` e `/owner/tenants` passam a exigir explicitamente o middleware de Super Admin, preservando o fluxo de suporte/impersonation.

- Adicionada a página Tailwind **Mesas & Sala**, disponível apenas para tenants restaurante.
- Implementados os estados operacionais livre, ocupada, reservada e limpeza.
- Adicionado vínculo opcional entre encomendas e mesas; a mesa é marcada como ocupada ao abrir uma comanda.
- Documentada a operação restaurante em `docs/07_VERTICAL_MODULES.md`.

## [1.0.18] - 2026-09-02 - Control Center SaaS Executivo, Criação Rápida de Tenants, Impersonate e Certificados de Licença
### Added & Improved
- **Visualização e Cópia Instantânea de Chaves de Licença de Software (`owner/tenants/index.blade.php` e `owner/tenants/show.blade.php`):**
  - O Dono da plataforma agora pode visualizar a chave serial ativa (`ZBIZ-XXXX-XXXX-XXXX-XXXX`) de cada empresa diretamente na tabela do Control Center com botão de cópia rápida em 1 clique.
  - Alertas interativos com feedback em tempo real e visualização expansível do certificado completo assinado para instalações offline.
- **Emissão e Impressão de Certificados Oficiais de Licença (`owner/tenants/certificate.blade.php`):**
  - Criada tela de Certificado de Licença Oficial em alta definição com selo de autenticidade, dados da empresa, NUIT, plano, modo de operação, chave serial em destaque e assinatura digital SHA-256 HMAC.
  - Suporte total a impressão direta e exportação em PDF via botão dedicado (`window.print()`).
- **Modal de Registo Rápido de Novas Empresas (`TenantControlCenterController::store`):**
  - O Dono pode registar instantaneamente novas empresas/tenants através de um modal interativo: dados comerciais, setor de atividade, plano, modo de instalação (Cloud/Offline), validade inicial e criação automática do utilizador gestor com emissão imediata da licença de software.
- **Modo Suporte / Impersonate (`TenantControlCenterController::impersonate`):**
  - Permite ao Super Admin aceder ao ambiente operacional de qualquer tenant com 1 clique para prestar assistência e suporte técnico em tempo real.
- **Métricas Executivas SaaS:**
  - Adicionado cálculo em tempo real de MRR (Receita Recorrente Mensal), ARR estimado, total de licenças ativas, filiais e utilizadores do ecossistema.

---

## [1.0.17] - 2026-09-02 - Reformulação do Painel do Dono (Owner), Chaves Seriais de Software e Correção de Temas Light/Dark
### Added & Improved
- **Menu Dedicado e Especializado para o Dono / Super Admin (`layouts/app.blade.php`):**
  - Removidos menus operacionais específicos de tenant (PDV, catálogo de produtos, vendas locais) da visão do Dono.
  - Estruturada a navegação exclusiva para gestão SaaS: **Tenants & Clientes**, **Validação & Ativação de Licenças**, **Todos os Utilizadores Globais**, **Auditoria & Atividades**, **Definições do Sistema** e **Perfil do Owner**.
- **Chaves de Licença em Formato Padrão de Software (`LicenseService.php`, `LicenseKey.php`, `license/activate.blade.php` e `owner/tenants/show.blade.php`):**
  - Implementada geração automática de Chaves Seriais legíveis no formato `ZBIZ-XXXX-XXXX-XXXX-XXXX` (ex: `ZBIZ-4F92-K81M-Q7P3-9A2E`).
  - Suporte de dupla ativação: tanto pelo código serial legível quanto pelo certificado token assinado criptograficamente.
- **Gestão Global de Utilizadores com Filtro por Empresa (`UserController.php` e `users/index.blade.php`):**
  - Para o Super Admin, a listagem de utilizadores agora exibe a coluna **Empresa / Tenant** com badge identificador e filtro seletivo por tenant.
- **Correção da Inconsistência de Cores nos Temas Light e Dark (`layouts/app.blade.php`):**
  - Eliminada sobreposição de `text-white` no tema claro e harmonizado o contraste tipográfico através de `var(--app-text)`.

---

## [1.0.16] - 2026-09-02 - Suporte Universal a Registo e Venda de Serviços para Todas as Empresas (Farmácias, Retalho, Gráfica)
### Added & Improved
- **Flexibilização do Módulo de Catálogo & Serviços (`ProductController.php`, `products/create.blade.php`, `products/edit.blade.php` e `products/index.blade.php`):**
  - Todas as empresas (Farmácias, Supermercados, Reprografia, Lojas de Conveniência, etc.) agora podem cadastrar tanto **Produtos Físicos** (com controlo de stock, estoque mínimo, lotes e validade) quanto **Serviços Prestados** (sem controlo de stock, permitindo faturação ilimitada).
  - Formulários de criação e edição adaptativos com Alpine.js (`x-model="itemType"`):
    - Ao selecionar **Serviço**, os campos de inventário e validade são ocultados automaticamente, exibindo caixa explicativa e orientações contextuais (ex: Teste de Glicemia, Medição de Tensão Arterial, Aplicação de Injectáveis, Curativos, Entregas, Consultoria, Estamparia, Encadernação).
    - Ao selecionar **Produto Físico**, os campos de contagem de unidades, stock mínimo de segurança e lotes ANARME/FEFO permanecem ativos.
  - Abas de filtragem rápida e botões no cabeçalho do catálogo atualizados para acomodar serviços em todos os setores.

---

## [1.0.15] - 2026-09-02 - Redesign Completo do Relatório Especializado de Vendas com Dark Theme & Chart.js
### Added & Improved
- **Modernização Visual do Relatório Especializado de Vendas (`reports/sales_specialized.blade.php`):**
  - Migração completa do antigo estilo Bootstrap para a linguagem de design Tailwind CSS com suporte total a dark mode e paleta de cores temática do tenant (`tenant_theme()`).
  - Top Action Bar com atalhos rápidos para a Central de Relatórios, exportação direta em Excel, PDF e impressão térmica/A4.
  - Filtros avançados com seleção de período, meios de pagamento (Dinheiro, M-Pesa, e-Mola, Cartão, Transferência e Crédito/Fiado) e busca rápida por cliente.
  - 6 cartões de KPIs executivos: Total de Vendas, Receita Bruta, Custo Total (CMV), Lucro Operacional, Ticket Médio e Margem Real %.
  - 2 gráficos interativos com Chart.js (Evolução Diária com curvas de receita/lucro e Gráfico Donut de distribuição por meio de pagamento).
  - Tabela analítica de Desempenho por Método de Pagamento com barras de progresso e classificação de performance comercial.
  - Top 10 Vendedores e Top 10 Artigos/Serviços mais rentáveis do período.
  - Extrato Detalhado de Vendas com badges estilizados, margens em tempo real e links diretos para exibição e impressão de comprovativos.
- **Escopo e Segurança de Dados (`ReportController.php`):**
  - Injetado isolamento rigoroso por `tenant_id` e filial ativa nas consultas analíticas de `ReportController::salesReport`.

---

## [Unreleased]
### Fixed
- **Isolamento de Produtos no POS (`POSController.php`):**
  - Removido o bypass de escopos globais nas consultas do POS e mantido filtro explícito por `tenant_id`.
  - Validado que filtros por categoria só aceitam categorias do tenant atual.
  - Restringida a finalização da venda para aceitar apenas `customer_id` e `product_id` pertencentes ao tenant atual.
  - Protegida a impressão de recibos contra acesso a vendas de outro tenant.
- **Cache e estado do catálogo POS (`POSController.php` & `pos/index.blade.php`):**
  - Desativado cache HTTP em `/pos` e `/pos/search`, evitando catálogo antigo ao alternar entre empresas.
  - Adicionado escopo de tenant/filial no estado Alpine do POS e nas filas offline locais.
  - Forçada busca sem cache no JavaScript, com validação do `tenant_id` retornado pela API.
- **Métricas do Dashboard (`DashboardController.php`):**
  - Corrigido cálculo de vendas mensais para considerar o mês completo e tenant atual.
  - Ajustado filtro por operador para que administradores e gerentes vejam vendas do tenant/filial, enquanto caixas continuam restritos às próprias vendas.
- **Definições e identidade visual (`AdminController.php`, `tenancy.php`, `layouts/app.blade.php` & `settings/index.blade.php`):**
  - Configurações comerciais e de marca passam a ser priorizadas por tenant via `tenants.settings`.
  - Cor principal definida pelo utilizador agora alimenta utilitários CSS reais usados em botões, bordas, badges, foco e gradientes.
  - Adicionados parâmetros profissionais para prefixos de fatura/recibo, formato do talão e política de stock baixo.

## [1.0.13] - 2026-09-01 - Correção das Movimentações de Stock Físico, Dashboard em Tempo Real e Cores Dinâmicas de Marca
### Fixed
- **Movimentações de Stock & Rastreabilidade de Vendas (`Product.php`, `StockMovementController.php` & `stock_movements/index.blade.php`):**
  - Corrigido `Product::updateStock` para reconhecer produtos com tipo `'physical'` e `'product'`, garantindo a baixa de inventário e geração imediata de `StockMovement`.
  - Injeção obrigatória de `tenant_id` e `branch_id` no registro de movimentações.
  - Atualizada a listagem de movimentações com barra de pesquisa rápida, filtros por tipo (Entrada, Saída, Ajuste) e dropdown dinâmico de artigos no modal.
- **Restauração e Métricas do Painel Principal (`DashboardController.php`, `layouts/app.blade.php` & `dashboard/index.blade.php`):**
  - Corrigido erro de verificação de `$errors` que impedia o carregamento do dashboard.
  - Exibição de KPIs em tempo real (Vendas de Hoje, Faturação do Mês, Lucro Real, Fiados a Receber e Lista de Vendas Recentes).
- **Identidade Visual & Cores Personalizadas em Tempo Real (`tenancy.php` & `layouts/app.blade.php`):**
  - Implementado cálculo dinâmico de `hex_to_rgba` e injeção do código hexadecimal escolhido nas variáveis CSS e botões primários.

---

## [1.0.12] - 2026-09-01 - Correção da Listagem de Dívidas & Ajuste do Layout Responsivo da Sidebar para Firefox
### Fixed
- **Listagem e Escopo de Fiados & Dívidas (`DebtController.php`, `SaleController.php` & `debts/index.blade.php`):**
  - Corrigido `createDebtFromCreditSale` para injetar `tenant_id`, `branch_id` e `customer_id` nos registros de `Debt` e `DebtItem`.
  - Atualizada a consulta `DebtController::index` com fallback para exibir todas as dívidas legítimas do tenant ativo e cálculo preciso de KPIs.
  - Adicionada barra de pesquisa rápida e 4 cartões de indicadores (Total Pendente, Vencidas, Contas Ativas e Liquidadas) na tela de Dívidas.
- **Sobreposição da Barra Lateral no Firefox & Desktop (`layouts/app.blade.php`):**
  - Ajustado o elemento `<aside>` para usar posicionamento relativo em telas desktop (`lg:relative lg:z-30`), eliminando qualquer sobreposição ou ocultação de conteúdo no Firefox e em monitores com escalonamento de DPI.

---

## [1.0.11] - 2026-09-01 - Redesign Moderno de Venda Manual & Motor de Descontos Promocionais Automáticos e Manuais
### Added
- **Design Moderno e Responsivo da Venda Manual (`sales/manual-create.blade.php`):**
  - Interface construída com Tailwind CSS + Alpine.js, alinhada ao visual dark-mode e branding setorial do ZBIZ+.
  - Seletor rápido de catálogo com busca, estoque em tempo real e detecção de promoções ativas.
  - Edição de linhas livres, preços customizados, descontos por linha e desconto global (% ou valor fixo em MT).
  - Suporte completo a múltiplos meios de pagamento (Dinheiro, M-Pesa, Cartão POS e Venda a Crédito/Fiado com validação).
- **Motor de Descontos Promocionais Automáticos (`Product.php`, `ProductController.php` & `POSController.php`):**
  - Adicionadas colunas `promotional_price`, `is_on_promotion`, `promotion_discount_percent` e `promotion_ends_at` na tabela `products`.
  - Novos métodos no Model `Product`: `isOnPromotion()`, `effective_price`, `automatic_unit_discount` e `automatic_discount_percent`.
  - Exibição de badge `PROMO -X%` e preço de tabela riscado nos cards do POS 2.0 e dropdown da Venda Manual.
  - Aplicação automática do desconto promocional no carrinho do POS e no formulário de Venda Manual, mantendo a flexibilidade de aplicar descontos adicionais manualmente.

---

## [1.0.10] - 2026-09-01 - Menu Lateral Dinâmico por Setor, Upload de Logotipo e Cor da Marca, Filtro de Serviços no Catálogo e Venda Manual
### Added
- **Menu Lateral Adaptativo por Setor (`layouts/app.blade.php` & `tenancy.php`):**
  - Rótulo de catálogo dinâmico conforme o setor do tenant (`Artigos & Serviços` na Reprografia/Serigrafia, `Medicamentos & Farmácia` na Farmácia, `Artigos & Produtos` no Retalho).
  - Suporte a exibição do Logótipo oficial da empresa na barra lateral e cabeçalho.
- **Identidade Visual & Configurações Comerciais (`settings/index.blade.php` & `AdminController.php`):**
  - Upload de logótipo da empresa (`company_logo`) com suporte a PNG, JPG, SVG e WebP.
  - Seletor de cor primária da marca (`primary_color`) com seletor hexadecimal e persistência no JSON de configurações do tenant.
  - Parâmetros para concessão de crédito/fiado e permissão de descontos comerciais.
- **Filtros por Tipo & Estatísticas na Gestão de Produtos (`products/index.blade.php` & `ProductController.php`):**
  - Barra de navegação rápida com abas: **Todos os Artigos**, **Produtos Físicos**, **Serviços Prestados** e **Stock Baixo**.
  - Mini-cards com contadores dedicados de artigos físicos vs serviços disponíveis.
- **Venda a Prazo (Dívidas) e Descontos Rápidos no POS 2.0 (`pos/index.blade.php`):**
  - Seção de desconto global expansível com presets (5%, 10%, 15%) e valor fixo em MT.
  - Validação de cliente obrigatório para pagamentos via Fiado/Dívida e campo de valor de entrada imediata.
- **Atalho de Venda Manual (`sales/index.blade.php`):**
  - Botão de acesso direto para o formulário de Venda Manual (`sales.manual-create`).

---

## [1.0.9] - 2026-09-01 - Integração de Features POS do ReproSys, Filtros Avançados por Tipo & Isolamento Multi-Tenant na Gestão de Utilizadores
### Added
- **Filtros Avançados por Tipo no POS 2.0 (Portado do ReproSys):**
  - Adicionados botões de filtro rápido por tipo de artigo: **Todos**, **Produtos Físicos**, **Serviços** e **Stock Baixo**.
  - Barra inferior de estatísticas em tempo real no catálogo do POS: Total de artigos listados, contagem de produtos físicos e contagem de serviços.
  - Botão de limpeza rápida da caixa de busca (`x-show="searchQuery"`).
  - Suporte ao parâmetro `type` (`all`, `physical`, `service`, `low-stock`) em `POSController::searchProducts` e `fetchProductsList`.

### Fixed
- **Isolamento de Utilizadores por Tenant (`UserController.php`):**
  - Corrigida a listagem `UserController::index` e contadores estatísticos para filtrar estritamente `tenant_id` da empresa do utilizador autenticado (não-superadmin).
  - Injeção automática de `tenant_id` e `branch_id` em `UserController::store` na criação de novos colaboradores.
  - Escopo de folha de pagamento (`UserController::payroll`) filtrado pelo `tenant_id` da empresa.

---

## [1.0.8] - 2026-09-01 - Isolamento Rigoroso de Tenants no POS 2.0, Tenant FDS Multiservices & Central de Configurações do Sistema
### Added
- **Central de Configurações Gerais do Sistema (`settings/index.blade.php`):**
  - Tela completa e moderna para parametrização dos dados comerciais da empresa (Nome Comercial, NUIT fiscal, Telefone, E-mail, Endereço da Sede).
  - Seletor de setor de atividade (`business_type`: Reprografia, Farmácia, Retalho, Restaurante, Serviços) com adaptação dinâmica de tema e regras de negócio.
  - Configurações fiscais e de moeda padrão (MZN / MT, Taxa de IVA).
  - Parâmetros operacionais para Frente de Caixa POS e talão térmico de 80mm (Mensagem de Rodapé, Limite de Alerta de Estoque Mínimo, Ativação de Notificações).
  - Atalhos diretos para Modelos de Documentos e Registos de Auditoria.
  - Rota nomeada `admin.settings` e action `AdminController::updateSettings` com sincronização no model `Tenant` e chave-valor `Setting`.
- **Tenant FDS Multiservices (Gráfica, Reprografia & Serigrafia em Quelimane):**
  - Registado e configurado no seeder mestre (`OperationalMultiBranchSeeder.php`):
    - **Empresa:** FDS Multiservices (`reprography`, NUIT: `0049983822`, Tel: `+258 84 724 0296`, Quelimane).
    - **Filiais:** Sede Quelimane (`FDS-QUE-01`) e Oficina de Serigrafia & Estamparia (`FDS-OFI-02`).
    - **Equipa:** Filipe Domingos dos Santos (`filipe.santos@fdsmultiservices.com`), Armando Mabote (Gerente), Sónia Mucavele (Caixa), Paulo Nhantumbo (Stock).
    - **Catálogo:** Serviços (Fotocópias A4 P&B, Impressão A4 Cores, Encadernação, Serigrafia em Camisetas) e Artigos Físicos com estoque controlado por loja (Camisetas Básicas Brancas/Pretas, Camisas Pólo Piquet, Canecas Resinadas para Sublimação, Resmas de Papel A4).

### Fixed
- **Isolamento de Tenants na Frente de Caixa POS 2.0:**
  - Inclusão das rotas `/pos/*` dentro da camada de middleware de tenant (`IdentifyTenant`), garantindo que `TenantContext` seja sempre resolvido para chamadas de API e navegação.
  - Aplicação de cláusula explícita `where('tenant_id', $tenantId)` e `withoutGlobalScopes` em `POSController::searchProducts`, `index` e `storeSale`, impedindo vazamento de artigos ou categorias entre diferentes empresas (ex: medicamentos de farmácia aparecendo em gráficas).
  - Sincronização e expurgo de sessão de filial anterior durante o login (`AuthenticatedSessionController::store` e `IdentifyTenant`), garantindo consistência no chaveamento de empresas.
  - Correção na inicialização do Alpine.js no POS 2.0 para registrar o componente `Alpine.data('posApp')` antes do carregamento da biblioteca, prevenindo atrasos de renderização de catálogo.

---

## [1.0.7] - 2026-09-01 - Controle de Acesso por Perfis (RBAC), Restrição de Filiais & Modernização Integral de Views
### Added
- **Controle de Acesso por Papéis (RBAC) & Filtragem de Menus:**
  - **Operador de Caixa (`cashier`):** Menu focado exclusivamente em Frente de Caixa POS 2.0, Vendas, Fiados, Pedidos e Consulta de Artigos. Seletor de filiais renderizado como badge estático.
  - **Gestor de Stock (`stock_manager`):** Menu focado em Artigos, Categorias, Movimentos de Stock e Despesas/Compras. Sem acesso a POS e sem relatórios financeiros confidenciais.
  - **Administrador & Gerente:** Visão consolidada de todas as lojas, relatórios DRE, salários, gestão de equipa e alternância de filial ativa.
- **Proteção na Troca de Filial (`BranchController::switchBranch` & `IdentifyTenant`):**
  - Restringida a troca de filial no backend exclusivamente para utilizadores com `canSwitchBranch()` (`isAdmin()`, `isSuperAdmin()`, `isManager()`).
- **Modernização e Interligação Integral de Views do Sistema:**
  - **Módulo de Produtos (`products/`):** Listagem com 3 mini-KPIs, busca em tempo real, filtro por categoria, atalho para o relatório analítico (`products.report`) e botões diretos para Ficha Técnica (`show`), Edição (`edit`) e Exclusão. Relatório migrado de Bootstrap para Tailwind Dark Mode.
  - **Módulo de Encomendas (`orders/`):** Modernização de `edit.blade.php` e `report.blade.php`, com resumo de adiantamentos, saldo pendente e tabela dinâmica de artigos. Listagem com atalhos para `show`, `edit` e `duplicate`.
  - **Módulo de Fiados & Devedores (`debts/`):** Listagem interligada com `debtors-report`, extrato e modal/página direta de quitação e amortização.
  - **Central de Relatórios (`reports/`):** Modernização de Vendas Diárias (`daily_sales`), Vendas Mensais (`monthly_sales`), DRE / Lucro & Prejuízo (`profit_loss`) e Inventário Geral (`inventory`).
  - **Compatibilidade Multi-Driver de Banco de Dados:** Tratamento de funções de data agnósticas (SQLite `strftime` e MySQL `DATE_FORMAT`) no `ReportController`.
- **Suite de Testes de Auditoria SaaS (`SaasOperationalAuditTest.php`):** Expandida para 7 testes automatizados com 39 asserções cobrindo isolamento de tenants, isolamento de filiais, restrições de RBAC e integridade de renderização de todas as views.

---

## [1.0.6] - 2026-09-01 - Segunda Auditoria Funcional, Isolamento de Filiais, Enriquecimento do Histórico de Vendas & Arquitetura SaaS Control Center
### Fixed
- **Precedência de Filial no Middleware `IdentifyTenant`:** Corrigida a lógica de resolução de filial para priorizar a sessão ativa (`session('current_branch_id')`), permitindo que administradores e gestores alternem dinamicamente entre filiais no Topbar Switcher sem conflito com o `user->branch_id`.
- **Injeção de `branch_id` e `tenant_id` nos Controllers CRUD:** Garantido que vendas manuais (`SaleController`), despesas (`ExpenseController`), dívidas (`DebtController`), encomendas (`OrderController`) e movimentações de estoque (`StockMovementController`) gravem explicitamente `branch_id = current_branch_id()` e `tenant_id = current_tenant_id()`.
- **Filtro de Filial nas Listagens Operacionais:** Aplicado escopo de filial em `SaleController::index`, `ExpenseController::index`, `DebtController::index`, `OrderController::index` e `StockMovementController::index` para assegurar isolamento operacional entre estabelecimentos.
- **Relacionamentos em Models:** Adicionadas relações `User->branch()` e `User->tenant()` no model `User.php`.
- **Layout Anti-Overlap no Firefox:** Reestruturado o container de `layouts/app.blade.php` para arquitetura `h-screen w-screen overflow-hidden` com cabeçalho fixo `flex-shrink-0` (64px) e `<main>` rolável independente.
- **Consultas & Cálculos em Relatórios:** Corrigida query de vendas diárias, MySQL `ONLY_FULL_GROUP_BY` na análise ABC e cálculo de desvio padrão nativo em relatórios comparativos.
- **Gráfico de Vendas no Dashboard:** Corrigido binding de dados para `chartData.salesData` e adicionada série de despesas (`expensesData`).

### Added
- **Enriquecimento do Histórico & Detalhes da Venda (`sales/show.blade.php`):**
  - **Identificação & Faturação:** Número da venda, data/hora exata, filial de emissão, operador e cliente completo.
  - **Artigos & Lotes:** Tabela com SKU/Barras, Lote ANARME e Validade, quantidade, unidade, preço unitário, desconto e subtotal líquido.
  - **Pagamento & Caixa:** Badge do método de pagamento (Dinheiro, M-Pesa, e-Mola, Cartão POS, Fiado), valor entregue e troco devolvido.
  - **Auditoria de Estoque:** Rastreamento visual de movimentações de saída associadas (`StockMovement`).
  - **Ações de Impressão:** Botões diretos para Recibo Térmico (80mm) e Fatura A4 / PDF.
- **Listagem de Vendas com Contexto (`sales/index.blade.php`):** Badges visuais de filial, badges de método de pagamento e atalho de visualização rápida da fatura.
- **Seeder Operacional Multi-Tenant & Multi-Filial (`OperationalMultiBranchSeeder.php`):**
  - Configuração de 2 Empresas (Farmácia Muzinga e Supermercado Zambézia).
  - 4 Filiais operacionais (Matriz Maputo, Filial Matola, Sede Quelimane, Filial Mocuba).
  - 6 Perfis com credenciais prontas (`super_admin`, `admin`, `manager`, `cashier`, `stock_manager`, `staff`).
  - Dados de estoque por filial (`ProductBranch`), vendas e lotes isolados para validação cruzada.
- **Documentação da Central de Controle SaaS (`docs/05_SAAS_CONTROL_CENTER.md`):**
  - Especificação detalhada da hierarquia **Plataforma ZBPOS+ (Control Plane) → Tenant / Empresa → Filial / Loja → Utilizador**.
  - Matriz de conceitos: **Plan**, **Subscription**, **License**, **Entitlement**, **Feature Flags** e **Branch Context**.
  - Especificação do futuro painel de gestão administrativa do SaaS.

## [1.0.5] - 2026-09-01 - Modernização Total de Relatórios, Stock, Encomendas, Detalhes Show e Bloqueio de Stock no POS
### Fixed
- **Fluxo e Sentido Financeiro no Livro-Razão:** Corrigida a condição de exibição em `finances/index.blade.php` para validar o campo `direction === 'in'` (Entrada verde com `+`), garantindo que vendas no POS constem sempre como receita e entrada de caixa.
- **Bloqueio Rigoroso de Stock no POS:** Implementado bloqueio visual e lógico no frontend (`pos/index.blade.php`), impedindo o clique e a adição de artigos com stock zero/esgotado ou quantidades superiores ao stock disponível (permitido apenas para serviços).

### Added
- **Modernização das Telas de Operação em TailwindCSS:**
  - **Relatórios & Analítica (`reports/index.blade.php`):** Painel unificado com acesso aos 6 hubs analíticos (Vendas, Fluxo de Caixa, DRE/Lucros, Curva ABC, Despesas, Ruptura de Stock).
  - **Movimentações de Stock (`stock_movements/index.blade.php`):** Tabela completa com badges de Entrada, Saída e Ajuste, além de modal para novos registos.
  - **Encomendas & Pedidos (`orders/index.blade.php`):** Gestão de estados de produção, prazos de entrega e especificações.
  - **Ficha do Artigo / Medicamento (`products/show.blade.php`):** Visualização completa com margem bruta, stock e tabela FEFO de lotes.
  - **Detalhes da Fatura / Venda (`sales/show.blade.php`):** Detalhes da venda, cliente, operador e atalho para reimpressão térmica.
  - **Extrato do Fiado (`debts/show.blade.php`):** Histórico de amortizações e modal de quitação parcial/total.
  - **Detalhes da Encomenda (`orders/show.blade.php`):** Acompanhamento técnico e comercial do pedido.

## [1.0.4] - 2026-08-31 - Gestão Integrada de Lotes & Validades (ANARME / FEFO)
### Added
- **Registo Especializado de Medicamentos:** Adicionados campos de Número de Lote (`batch_number`), Data de Validade (`expiry_date`), Data de Fabrico (`manufacture_date`), Dosagem (`dosage`) e Princípio Ativo (`active_ingredient`) ao formulário de cadastro de produtos.
- **Indicadores Visuais de Validade no Catálogo:** Exibição da data de expiração do lote mais próximo (FEFO) com badges coloridas de alerta (verde para seguro, âmbar para vencimento próximo < 60 dias e vermelho para expirado).
- **Migration `2026_08_31_000007_add_sku_and_dosage_to_products_table.php`:** Suporte a SKU, dosagem e princípio ativo na tabela `products`.

## [1.0.3] - 2026-08-31 - Correção de Coluna de Despesas & Categorias Setoriais Rigorosas
### Fixed
- **Coluna `receipt_number` na tabela `expenses`:** Adicionada migration `2026_08_31_000006_add_receipt_number_to_expenses_table.php` resolvendo a exceção SQLSTATE ao consultar despesas e relatórios.
- **Categorização Rigorosa por Setor (`TenantSectorService`):** Correção do seeding e vinculação de categorias especializadas para Farmácias (Antibióticos, Analgésicos, Vitaminas, Pediátricos, Psicotrópicos ANARME), eliminando categorias de mercearia/bebidas em tenants farmacêuticos.

### Added
- **Modernização Global de Telas em TailwindCSS:** Atualizadas as views de **Vendas (`sales/index.blade.php`)**, **Fiados & Dívidas (`debts/index.blade.php`)**, **Despesas (`expenses/index.blade.php`)**, **Finanças/Livro-Razão (`finances/index.blade.php`)** e **Cadastro de Produtos (`products/create.blade.php`)** com o design escuro e responsivo do ZBIZ POS 2.0.

## [1.0.2] - 2026-08-31 - Migração Total para TailwindCSS & Temas Setoriais Dinâmicos
### Fixed
- **Inconsistência de Coluna `status` vs `is_active`:** Corrigidas todas as consultas SQL em `Category`, `ProductController`, `OrderController`, `CategoryController` e `SearchController`, adicionando o scope `scopeActive()` no model `Category`.
- **Rotas e Parâmetros de Finanças:** Correção de chamadas de rota em `layouts/app.blade.php`.

### Added
- **Helper de Temas Setoriais (`tenant_theme`):** Suporte nativo a paletas de cores automáticas por ramo de atividade:
  - 🟢 **Farmácia & Saúde (`pharmacy`):** Verde Esmeralda & Teal (`emerald-500` / `teal-600`)
  - 🔵 **Retalho & Comércio (`retail`):** Azul Céu & Indigo (`sky-500` / `indigo-600`)
  - 🟠 **Restaurante & Bar (`restaurant`):** Laranja & Rosa Quente (`orange-500` / `rose-600`)
  - 🟣 **Gráfica & Reprografia (`reprography`):** Violeta & Púrpura (`violet-500` / `purple-600`)
  - 🔷 **Prestação de Serviços (`services`):** Ciano & Teal (`teal-500` / `cyan-600`)
- **Migração Completa de Layout para TailwindCSS + Alpine.js:** Substituição do Bootstrap no `layouts/app.blade.php`, `dashboard/index.blade.php`, `categories/index.blade.php` e `products/index.blade.php` no mesmo design moderno, escuro e responsivo do ZBIZ POS 2.0.

## [1.0.1] - 2026-08-31 - Modernização de Views & Onboarding SaaS Interativo
### Added
- **Landing Page Oficial (`welcome.blade.php`):** Design moderno com TailwindCSS e Alpine.js, seletor de setor interativo (Retalho, Farmácia, Gráfica, Serviços), planos em Meticais (MZN) e chamada para trial de 30 dias.
- **SaaS Onboarding Wizard (`register.blade.php`):** Assistente passo a passo em 3 etapas para criação autónoma de empresas (Tenant), filial inicial, contas de caixa/M-Pesa e categorias padrão por setor.
- **Identidade Visual ZBIZ+ no Painel:** Atualização de `layouts/app.blade.php` com badge de filial ativa (`Loja Principal`), indicador de subscrição (`Plano Trial 30 Dias`) e atalho direto em destaque para o **ZBIZ POS 2.0**.
- **Testes de Navegação & Onboarding:** Suite `tests/Feature/Auth/TenantOnboardingTest.php` validada e testada no browser com 100% de aprovação.

## [1.0.0] - 2026-08-30 - Lançamento Oficial do ZBIZ+
### Added
- **M-Pesa C2B Integration:** Driver `MpesaDriver` com normalização de números moçambicanos (prefixos 84/85 e DDI 258) e chamadas STK Push.
- **Webhooks & Idempotência:** Endpoint `/api/webhooks/mpesa` com validação de assinatura, atualização atómica de pagamentos de subscrição e prevenção contra processamento duplicado de transações.
- **Gateway Manager:** `PaymentGatewayManager` extensível para múltiplos provedores móveis de Moçambique.
- Suite de testes de integração financeira e pagamentos móveis (`tests/Feature/Payments/`).

## [0.6.0] - 2026-08-30 - Fase 6: Verticais Especializadas (Farmácia ANARME & Gráfica/Insumos)
### Added
- **ZBIZ Pharmacy:** Model `ProductBatch` com controlo rigoroso de lotes e datas de validade.
- **Algoritmo FEFO (First Expired, First Out):** `PharmacyBatchService` para dispensação prioritária automática dos lotes com vencimento mais próximo e quarentena de lotes expirados.
- **Alertas Regulatórios ANARME:** Consultas parametrizadas de risco de validade (30, 60 e 90 dias).
- **Receitas Médicas:** Model `Prescription` para rastreio de prescrições e psicotrópicos.
- **ZBIZ Repro / Gráfica:** Model `ProductInsumo` e `InsumoManagerService` para vinculação de matérias-primas e dedução automática de stock na venda de serviços de cópia/impressão.
- Suite de testes automatizados para farmácia e gráfica (`tests/Feature/Verticals/`).

## [0.5.0] - 2026-08-30 - Fase 5: ZBIZ POS 2.0 (Frente de Caixa Rápida)
### Added
- Módulo `POSController` com endpoints ultrarrápidos para busca por código de barras, SKU e categorização dinâmica.
- Interface moderna Blade + TailwindCSS + Alpine.js (`resources/views/pos/index.blade.php`) com suporte a atalhos de teclado (F2 Buscar, F4 Cliente, F9 Checkout, ESC Cancelar).
- Suporte a multi-pagamento no POS: Dinheiro com cálculo de troco, M-Pesa, Cartão POS e Fiado/Crédito com criação imediata de dívida.
- Suporte a modo Offline-First com enfileiramento em cache local e sincronização automática via `/pos/sync-offline`.
- Layout de Impressão Térmica 80mm/58mm (`resources/views/pos/receipt.blade.php`) com NUIT do cliente/empresa, operador e data.
- Testes automatizados de frente de caixa e checkout (`tests/Feature/POS/POSCheckoutTest.php`).

## [0.4.0] - 2026-08-30 - Fase 4: Motor de Licenciamento SaaS & Planos
### Added
- Modelos `Plan`, `Subscription` e `SubscriptionPayment` para monetização SaaS.
- Seeder `PlanSeeder` com planos comerciais para o mercado moçambicano (Starter, Pro, Business, Pharmacy+, Enterprise).
- `SubscriptionService` para gestão de períodos de avaliação (Trial 30 dias), subscrições, upgrades e validação de limites de utilizadores e filiais.
- Middleware `CheckSubscriptionStatus` com fallback seguro para modo somente-leitura em caso de expiração da subscrição.
- Testes automatizados de licenciamento e limites (`tests/Feature/Licensing/SubscriptionAndPlansTest.php`).

## [0.3.0] - 2026-08-30 - Fase 3: Core ERP & Multi-Branch
### Added
- Entidade `Customer` com validação de NUIT moçambicano, controlo de limite de crédito (`credit_limit`) e recálculo automático de saldo devedor.
- Entidade `Supplier` para gestão de fornecedores e contas a pagar.
- Gestão de stock multi-filial com `ProductBranch` (quantidades isoladas por loja e armazém).
- Módulo de transferência de stock entre filiais (`StockTransfer` e `StockManagerService`).
- Turnos e Fecho de Caixa com contagem cega (`CashShift`).
- `FinancialLedgerService` aprimorado para auditoria atómica, sincronização de vendas/despesas e métricas consolidadas ou por filial.
- Suite completa de testes automatizados (`tests/Feature/Core/`).

## [0.2.0] - 2026-08-30 - Fase 2: Multi-Tenancy & Segurança
### Added
- Model `Tenant` para gestão de empresas clientes e subscrições.
- Model `Branch` para suporte nativo a múltiplas filiais por empresa.
- Trait `BelongsToTenant` com `TenantScope` automático.
- Middleware `IdentifyTenant` para resolução de tenant via subdomínio, sessão e headers de API.
- Testes de isolamento estrito entre tenants (`TenantIsolationTest`).

---

## [0.1.0] - 2026-08-30 - Fase 0 & 1: Discovery, Audit & Setup Base
### Added
- Inicialização do repositório oficial `ZBIZ_PLUS` (git@github.com:filipeive/ZBIZ_PLUS.git).
- Branches `main` e `develop` configuradas e sincronizadas remotamente.
- Constituição Técnica de Engenharia em `docs/00_ENGINEERING_GUIDE.md`.
- Documentação completa de visão, mercado moçambicano, roadmap, arquitetura, multi-tenancy, esquema de dados, POS, farmácia (ANARME), fiscalidade e pagamentos M-Pesa (`docs/01_...` a `docs/12_...`).
- ADRs 0001, 0002 e 0003 em `docs/adr/`.
- Diretrizes para Agentes de IA em `agents/AGENT_GUIDELINES.md` e `agents/PROJECT_CONTEXT.md`.
- Relatório completo de auditoria e descoberta arquivado em `docs/00_DISCOVERY_AUDIT_REPORT.md`.

### Fixed
- Correção de queries incompatíveis de `SHOW INDEX` e `information_schema` nas migrações legadas, viabilizando execução sem falhas em SQLite e MySQL.
- Consolidação da migração inicial do core business (`0001_01_01_000003_create_core_business_tables.php`).
