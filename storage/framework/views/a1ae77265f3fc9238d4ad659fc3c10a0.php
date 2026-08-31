<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('title-icon', 'fa-chart-line'); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
<style>
    /* ===== DASHBOARD CARDS OTIMIZADOS ===== */
    .dashboard-card {
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        background: var(--card-bg);
    }
    
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    /* ===== METRIC CARDS COMPACTOS ===== */
    .metric-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-blue), #4A90E2);
    }
    
    .metric-card.success::before {
        background: linear-gradient(90deg, var(--success-green), #22C55E);
    }
    
    .metric-card.warning::before {
        background: linear-gradient(90deg, var(--warning-orange), #F59E0B);
    }
    
    .metric-card.danger::before {
        background: linear-gradient(90deg, var(--danger-red), #EF4444);
    }
    
    .metric-card.info::before {
        background: linear-gradient(90deg, var(--info-blue), #3B82F6);
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }
    
    .metric-icon.primary {
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
    }
    
    .metric-icon.success {
        background: linear-gradient(135deg, var(--success-green), #22C55E);
    }
    
    .metric-icon.warning {
        background: linear-gradient(135deg, var(--warning-orange), #F59E0B);
    }
    
    .metric-icon.danger {
        background: linear-gradient(135deg, var(--danger-red), #EF4444);
    }
    
    .metric-icon.info {
        background: linear-gradient(135deg, var(--info-blue), #3B82F6);
    }
    
    .metric-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
        line-height: 1;
    }
    
    .metric-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .metric-change {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .metric-change.positive {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-green);
    }
    
    .metric-change.negative {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-red);
    }
    
    .metric-change.neutral {
        background: rgba(108, 117, 125, 0.1);
        color: var(--text-secondary);
    }
    
    /* ===== QUICK ACTIONS COMPACTAS ===== */
    .quick-action-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1rem;
        text-align: center;
        transition: var(--transition);
        text-decoration: none;
        color: var(--text-primary);
        display: block;
        box-shadow: var(--shadow-sm);
    }
    
    .quick-action-card:hover {
        border-color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        color: var(--primary-blue);
        text-decoration: none;
    }
    
    .quick-action-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--border-radius);
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1.25rem;
    }
    
    .quick-action-card h6 {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .quick-action-card small {
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    
    /* ===== ALERT CARDS ===== */
    .alert-card {
        border-left: 3px solid;
        border-radius: var(--border-radius);
        background: var(--card-bg);
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .alert-warning {
        border-left-color: var(--warning-orange);
        background-color: rgba(255, 165, 0, 0.05);
    }
    
    .alert-success {
        border-left-color: var(--success-green);
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    /* ===== CHART CONTAINERS ===== */
    .chart-container {
        position: relative;
        height: 300px;
        padding: 0.5rem;
    }
    
    .chart-container.small {
        height: 200px;
    }
    
    /* ===== ACTIVITY ITEMS ===== */
    .activity-item {
        padding: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .activity-item:hover {
        background-color: var(--content-bg);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    /* ===== STATS GRID ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    /* ===== HEADER SECTION COMPACTO ===== */
    .dashboard-header {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
    }
    
    /* ===== LOADING SKELETON ===== */
    .skeleton-loading {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .metric-value {
            font-size: 1.5rem;
        }
        
        .chart-container {
            height: 220px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .metric-card {
            padding: 1rem;
        }
        
        .quick-action-card {
            padding: 0.875rem;
        }
    }
    
    @media (max-width: 576px) {
        .metric-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
        
        .quick-action-icon {
            width: 40px;
            height: 40px;
            font-size: 1.125rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <li class="breadcrumb-item active">Dashboard Executivo</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Compacto -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1" id="welcome-greeting">
                    Bom dia, <?php echo e(explode(' ', auth()->user()->name)[0]); ?>! 👋
                </h5>
                <p class="text-muted mb-0 small">
                    Resumo completo do seu negócio em tempo real
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex align-items-center justify-content-md-end">
                    <i class="fas fa-clock text-primary me-2"></i>
                    <div>
                        <div class="fw-bold" id="current-time"><?php echo e(now()->format('H:i')); ?></div>
                        <small class="text-muted"><?php echo e(now()->translatedFormat('l')); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Compactas -->
    <?php if(userCanAny(['create_sales', 'create_products', 'view_reports', 'view_expenses'])): ?>
    <div class="row g-2 mb-3">
        <?php if(userCan('create_sales')): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="<?php echo e(route('sales.create')); ?>" class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h6>Nova Venda</h6>
                <small>PDV Rápido</small>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if(userCan('create_products')): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="<?php echo e(route('products.create')); ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--success-green), #22C55E);">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h6>Novo Produto</h6>
                <small>Cadastrar</small>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if(userCan('view_reports')): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="<?php echo e(route('reports.index')); ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--warning-orange), #F59E0B);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h6>Relatórios</h6>
                <small>Análises</small>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if(userCan('view_expenses')): ?>
        <div class="col-6 col-md-3 col-lg-2">
            <a href="<?php echo e(route('expenses.create')); ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--danger-red), #EF4444);">
                    <i class="fas fa-receipt"></i>
                </div>
                <h6>Nova Despesa</h6>
                <small>Registrar</small>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="col-6 col-md-3 col-lg-2">
            <a href="<?php echo e(route('orders.create')); ?>" class="quick-action-card">
                <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--info-blue), #3B82F6);">
                    <i class="fas fa-truck"></i>
                </div>
                <h6>Novo Pedido</h6>
                <small>Criar</small>
            </a>
        </div>
        
        <div class="col-6 col-md-3 col-lg-2">
            <a href="<?php echo e(route('debts.create')); ?>" class="quick-action-card">
            <div class="quick-action-icon" style="background: linear-gradient(135deg, #FF6B6B, #EE5A6F);">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <h6>Nova Dívida</h6>
            <small>Registar</small>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Métricas Principais Compactas -->
    <div class="stats-grid">
        <div class="metric-card">
            <div class="d-flex align-items-center">
                <div class="metric-icon primary me-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="metric-value" id="today-sales">
                        <?php echo e(number_format($todaySales, 2, ',', '.')); ?>

                    </div>
                    <div class="metric-label">Vendas de Hoje</div>
                    <div class="metric-change <?php echo e($salesChangeDirection); ?>" id="today-sales-change">
                        <i class="fas <?php echo e($salesChangeIcon); ?>"></i>
                        <span id="today-sales-change-percent"><?php echo e($salesChangePercent); ?></span>%
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-card danger">
            <div class="d-flex align-items-center">
                <div class="metric-icon danger me-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="metric-value" id="today-outflows">
                        <?php echo e(number_format($todayOutflows, 2, ',', '.')); ?>

                    </div>
                    <div class="metric-label">Saídas de Hoje</div>
                    <div class="metric-change <?php echo e($outflowsChangeDirection); ?>" id="today-outflows-change">
                        <i class="fas <?php echo e($outflowsChangeIcon); ?>"></i>
                        <span id="today-outflows-change-percent"><?php echo e($outflowsChangePercent); ?></span>%
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-card info">
            <div class="d-flex align-items-center">
                <div class="metric-icon info me-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="metric-value">
                        <?php echo e(number_format($monthReceived, 2, ',', '.')); ?>

                    </div>
                    <div class="metric-label">Recebido no Mês</div>
                    <div class="metric-change <?php echo e($monthReceivedChangeDirection); ?>">
                        <i class="fas <?php echo e($monthReceivedChangeIcon); ?>"></i>
                        <?php echo e($monthReceivedChangePercent); ?>%
                    </div>
                </div>
            </div>
        </div>

        <div class="metric-card warning">
            <div class="d-flex align-items-center">
            <div class="metric-icon warning me-3">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="flex-grow-1">
                <div class="metric-value" id="low-stock-count">
                <?php echo e($lowStockProducts->count()); ?>

                </div>
                <div class="metric-label">Estoque Baixo</div>
                <div class="metric-change">
                <?php if($lowStockProducts->count() > 0): ?>
                    <i class="fas fa-exclamation-triangle"></i>
                    Atenção
                <?php else: ?>
                    <i class="fas fa-check-circle"></i>
                    OK
                <?php endif; ?>
                </div>
            </div>
            </div>
        </div>

        
        <?php if(session('dashboard_alert')): ?>
            <div class="alert-item metric-card">
            <div class="alert-icon" style="background: 
                <?php if(session('dashboard_alert')['type'] === 'success'): ?> var(--success-green)
                <?php elseif(session('dashboard_alert')['type'] === 'warning'): ?> var(--warning-orange)
                <?php elseif(session('dashboard_alert')['type'] === 'error'): ?> var(--danger-red)
                <?php else: ?> var(--info-blue) <?php endif; ?>">
                <i class="fas fa-
                <?php if(session('dashboard_alert')['type'] === 'success'): ?> check
                <?php elseif(session('dashboard_alert')['type'] === 'warning'): ?> exclamation-triangle
                <?php elseif(session('dashboard_alert')['type'] === 'error'): ?> exclamation-circle
                <?php else: ?> info <?php endif; ?>"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold"><?php echo e(session('dashboard_alert')['message']); ?></div>
                <small class="text-muted">Agora mesmo</small>
            </div>
            </div>
        <?php endif; ?>

        
        <?php if($lowStockProducts->count() > 0): ?>
            <div class="alert-item metric-card">
            <div class="alert-icon pulse" style="background: var(--danger-red);">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">Estoque Baixo Detectado</div>
                <small class="text-muted">
                <?php echo e($lowStockProducts->count()); ?> produto(s) precisam de reposição:
                <?php echo e($lowStockProducts->take(3)->pluck('name')->join(', ')); ?>

                <?php if($lowStockProducts->count() > 3): ?> e outros... <?php endif; ?>
                </small>
            </div>
            <a href="<?php echo e(route('products.index', ['filter' => 'low_stock'])); ?>" class="btn btn-sm btn-outline-danger">
                Ver Produtos
            </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alertas -->
    <?php if(session('dashboard_alert') || $lowStockProducts->count() > 0): ?>
    <div class="row mb-3">
        <div class="col-12">
            <?php if(session('dashboard_alert')): ?>
            <div class="alert-card alert-<?php echo e(session('dashboard_alert')['type']); ?>">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong><?php echo e(session('dashboard_alert')['message']); ?></strong>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($lowStockProducts->count() > 0): ?>
            <div class="alert-card alert-warning">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong><?php echo e($lowStockProducts->count()); ?> produto(s)</strong> com estoque baixo
                        </div>
                    </div>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-warning btn-sm">
                        Ver Produtos
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Métricas Adicionais Compactas -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-coins text-success mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-success mb-1">MT <?php echo e(number_format($monthRealProfit, 2, ',', '.')); ?></h5>
                <small class="text-muted">Lucro Real do Mês</small>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-users text-info mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-info mb-1"><?php echo e($monthActiveCustomers); ?></h5>
                <small class="text-muted">Clientes Ativos</small>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-shopping-cart text-primary mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-primary mb-1"><?php echo e($todayProductsSold); ?></h5>
                <small class="text-muted">Vendidos Hoje</small>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-percentage text-warning mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-warning mb-1"><?php echo e(number_format($monthNetMargin, 1)); ?>%</h5>
                <small class="text-muted">Margem Líquida</small>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center border-primary" style="background: rgba(59, 130, 246, 0.05);">
                <i class="fas fa-university text-primary mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-primary mb-1">MT <?php echo e(number_format($totalRealValue, 2, ',', '.')); ?></h5>
                <small class="text-dark fw-bold">VALOR REAL DO NEGÓCIO</small>
                <div class="x-small text-muted" style="font-size: 0.65rem;">Capital + Dívidas a Receber</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-wallet text-success mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-success mb-1">MT <?php echo e(number_format($currentCapital, 2, ',', '.')); ?></h5>
                <small class="text-muted">Capital em Caixa</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-file-invoice-dollar text-warning mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="text-warning mb-1">MT <?php echo e(number_format($accountsReceivable, 2, ',', '.')); ?></h5>
                <small class="text-muted">A Receber (Dívidas)</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dashboard-card p-3 text-center">
                <i class="fas fa-exchange-alt <?php echo e($monthNetCashFlow >= 0 ? 'text-success' : 'text-danger'); ?> mb-2" style="font-size: 1.5rem;"></i>
                <h5 class="<?php echo e($monthNetCashFlow >= 0 ? 'text-success' : 'text-danger'); ?> mb-1">
                    MT <?php echo e(number_format($monthNetCashFlow, 2, ',', '.')); ?>

                </h5>
                <small class="text-muted">Fluxo Líquido do Mês</small>
            </div>
        </div>
    </div>

    <div class="dashboard-card mb-3">
        <div class="card-header bg-transparent border-0 py-3">
            <h6 class="mb-0">
                <i class="fas fa-heartbeat text-danger me-2"></i>
                Saúde do Negócio
            </h6>
        </div>
        <div class="card-body pt-0">
            <div class="row g-2">
                <div class="col-6 col-lg-3">
                    <div class="dashboard-card p-3 h-100" style="background: rgba(59, 130, 246, 0.06);">
                        <small class="text-muted d-block mb-1">Lucro Bruto</small>
                        <h5 class="text-primary mb-1">MT <?php echo e(number_format($monthGrossProfit, 2, ',', '.')); ?></h5>
                        <small class="text-muted"><?php echo e(number_format($monthGrossMargin, 1)); ?>% das vendas do mês</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dashboard-card p-3 h-100" style="background: rgba(16, 185, 129, 0.06);">
                        <small class="text-muted d-block mb-1">Lucro Real</small>
                        <h5 class="mb-1 <?php echo e($monthRealProfit >= 0 ? 'text-success' : 'text-danger'); ?>">
                            MT <?php echo e(number_format($monthRealProfit, 2, ',', '.')); ?>

                        </h5>
                        <small class="text-muted">Após custo dos produtos e despesas</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dashboard-card p-3 h-100" style="background: rgba(239, 68, 68, 0.06);">
                        <small class="text-muted d-block mb-1">Despesas</small>
                        <h5 class="text-danger mb-1">MT <?php echo e(number_format($monthExpenses, 2, ',', '.')); ?></h5>
                        <small class="text-muted">Custos operacionais do mês</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="dashboard-card p-3 h-100" style="background: rgba(245, 158, 11, 0.06);">
                        <small class="text-muted d-block mb-1">ROI</small>
                        <h5 class="mb-1 <?php echo e($monthRoi >= 0 ? 'text-warning' : 'text-danger'); ?>">
                            <?php echo e(number_format($monthRoi, 1)); ?>%
                        </h5>
                        <small class="text-muted">Retorno sobre investimento do mês</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-3">
        <!-- Gráfico Principal -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Vendido vs Despesas (7 dias)
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" onclick="updateChartPeriod(7)">7d</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChartPeriod(30)">30d</button>
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fluxo de Caixa -->
        <div class="col-lg-4">
            <div class="dashboard-card">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-exchange-alt text-success me-2"></i>
                        Fluxo de Caixa Real
                    </h6>
                </div>
                <div class="card-body py-2">
                    <div class="chart-container small">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                    
                    <div class="row mt-2 text-center">
                        <div class="col-4">
                            <small class="text-success d-block">Entradas</small>
                            <strong class="small"><?php echo e(number_format(array_sum($cashFlowChartData['inflowsData']), 0)); ?></strong>
                        </div>
                        <div class="col-4">
                            <small class="text-danger d-block">Saídas</small>
                            <strong class="small"><?php echo e(number_format(array_sum($cashFlowChartData['outflowsData']), 0)); ?></strong>
                        </div>
                        <div class="col-4">
                            <small class="text-info d-block">Líquido</small>
                            <strong class="small"><?php echo e(number_format(array_sum($cashFlowChartData['netFlowData']), 0)); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção Inferior -->
    <div class="row g-3">
        <!-- Atividades Recentes -->
        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-history text-info me-2"></i>
                        Atividades Recentes
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $recentSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shopping-cart text-success me-2"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong class="small">Venda #<?php echo e($sale->id); ?></strong>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?php echo e($sale->user->name ?? 'Sistema'); ?> • MT <?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?>

                                        </div>
                                    </div>
                                    <small class="text-muted"><?php echo e($sale->created_at->diffForHumans()); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="activity-item text-center py-4">
                        <i class="fas fa-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0 small">Nenhuma atividade recente</p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($recentSales->count() > 0): ?>
                    <div class="card-footer bg-transparent border-0 text-center py-2">
                        <a href="<?php echo e(route('sales.index')); ?>" class="btn btn-outline-primary btn-sm">
                            Ver todas
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comparativo Mensal -->
        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-warning me-2"></i>
                        Comparativo Mensal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Faturamento Vendido</small>
                                <h6 class="text-success mb-0">MT <?php echo e(number_format($monthSales, 2, ',', '.')); ?></h6>
                            </div>
                            <span class="badge bg-success">
                                <i class="fas fa-arrow-up"></i> <?php echo e($monthSalesChangePercent); ?>%
                            </span>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Recebido</small>
                                <h6 class="text-primary mb-0">MT <?php echo e(number_format($monthReceived, 2, ',', '.')); ?></h6>
                            </div>
                            <small class="text-muted">Saídas: <?php echo e(number_format($monthOutflows, 0)); ?></small>
                        </div>
                    </div>
                    
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Despesas</small>
                                <h6 class="text-danger mb-0">MT <?php echo e(number_format($monthExpenses, 2, ',', '.')); ?></h6>
                            </div>
                            <small class="text-muted">vs <?php echo e(number_format($prevMonthExpenses, 0)); ?></small>
                        </div>
                    </div>
                    
                    <div class="p-3 rounded" style="background: rgba(91, 155, 213, 0.1);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Lucro Real</small>
                                <h6 class="text-primary mb-0">MT <?php echo e(number_format($monthRealProfit, 2, ',', '.')); ?></h6>
                            </div>
                            <span class="badge <?php echo e($monthProfitChangePercent > 0 ? 'bg-success' : 'bg-danger'); ?>">
                                <?php echo e($monthProfitChangePercent > 0 ? '+' : ''); ?><?php echo e($monthProfitChangePercent); ?>%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let salesChart, cashFlowChart;
let chartUpdateInterval;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando Dashboard...');
    
    // Saudação dinâmica
    function updateGreeting() {
        const hour = new Date().getHours();
        const greetingElement = document.getElementById('welcome-greeting');
        if (!greetingElement) return;
        
        const name = '<?php echo e(explode(" ", auth()->user()->name)[0]); ?>';
        let greeting = hour < 12 ? 'Bom dia' : hour < 18 ? 'Boa tarde' : 'Boa noite';
        
        greetingElement.innerHTML = `${greeting}, ${name}! 👋`;
    }
    
    // Relógio
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('pt-PT', { 
            hour: '2-digit', 
            minute: '2-digit'
        });
        const clockElement = document.getElementById('current-time');
        if (clockElement) clockElement.textContent = timeString;
    }
    
    // Configuração dos gráficos
    const chartColors = {
        primary: getComputedStyle(document.documentElement).getPropertyValue('--primary-blue').trim() || '#5B9BD5',
        success: getComputedStyle(document.documentElement).getPropertyValue('--success-green').trim() || '#28A745',
        danger: getComputedStyle(document.documentElement).getPropertyValue('--danger-red').trim() || '#DC3545',
        info: getComputedStyle(document.documentElement).getPropertyValue('--info-blue').trim() || '#17A2B8'
    };

    const defaultChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-primary'),
                    font: { family: 'Segoe UI, Inter, sans-serif', size: 11, weight: '500' },
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 10,
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': MT ' + context.parsed.y.toLocaleString('pt-PT', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary'),
                    font: { family: 'Segoe UI, Inter, sans-serif', size: 10, weight: '500' }
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    color: getComputedStyle(document.documentElement).getPropertyValue('--text-secondary'),
                    font: { family: 'Segoe UI, Inter, sans-serif', size: 10 },
                    callback: function(value) {
                        return 'MT ' + value.toLocaleString('pt-PT');
                    }
                }
            }
        }
    };

    // Gráfico de Vendas
    function initSalesChart() {
        const salesCtx = document.getElementById('salesChart');
        if (!salesCtx) return;

        if (salesChart) salesChart.destroy();

        try {
            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($salesChartData['labels'], 15, 512) ?>,
                    datasets: [{
                        label: 'Vendas',
                        data: <?php echo json_encode($salesChartData['salesData'], 15, 512) ?>,
                        borderColor: chartColors.primary,
                        backgroundColor: chartColors.primary + '20',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }, {
                        label: 'Despesas',
                        data: <?php echo json_encode($salesChartData['expensesData'], 15, 512) ?>,
                        borderColor: chartColors.danger,
                        backgroundColor: chartColors.danger + '20',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: defaultChartOptions
            });
            
            console.log('✅ Gráfico de vendas inicializado');
        } catch (error) {
            console.error('❌ Erro ao inicializar gráfico de vendas:', error);
        }
    }

    // Gráfico de Fluxo de Caixa
    function initCashFlowChart() {
        const cashFlowCtx = document.getElementById('cashFlowChart');
        if (!cashFlowCtx) return;

        if (cashFlowChart) cashFlowChart.destroy();

        try {
            cashFlowChart = new Chart(cashFlowCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($cashFlowChartData['labels'], 15, 512) ?>,
                    datasets: [{
                        label: 'Entradas',
                        data: <?php echo json_encode($cashFlowChartData['inflowsData'], 15, 512) ?>,
                        backgroundColor: chartColors.success + 'CC',
                        borderColor: chartColors.success,
                        borderWidth: 1,
                        borderRadius: 4
                    }, {
                        label: 'Saídas',
                        data: <?php echo json_encode($cashFlowChartData['outflowsData'], 15, 512) ?>,
                        backgroundColor: chartColors.danger + 'CC',
                        borderColor: chartColors.danger,
                        borderWidth: 1,
                        borderRadius: 4
                    }, {
                        label: 'Líquido',
                        data: <?php echo json_encode($cashFlowChartData['netFlowData'], 15, 512) ?>,
                        type: 'line',
                        borderColor: chartColors.info,
                        backgroundColor: chartColors.info + '20',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    ...defaultChartOptions,
                    scales: {
                        ...defaultChartOptions.scales,
                        y: {
                            ...defaultChartOptions.scales.y,
                            beginAtZero: false
                        }
                    }
                }
            });
            
            console.log('✅ Gráfico de fluxo de caixa inicializado');
        } catch (error) {
            console.error('❌ Erro ao inicializar gráfico de fluxo:', error);
        }
    }

    // Atualização de Métricas
    function updateDashboardMetrics() {
        fetch('<?php echo e(route("dashboard.metrics")); ?>', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('📊 Métricas atualizadas:', data);

            // Atualizar métricas principais
            updateMetricCard('today-sales', data.todaySales, data.salesChangePercent, data.salesChangeDirection, data.salesChangeIcon);
            updateMetricCard('today-outflows', data.todayOutflows, data.outflowsChangePercent, data.outflowsChangeDirection, data.outflowsChangeIcon);

            // Atualizar estoque baixo
            const lowStockElement = document.getElementById('low-stock-count');
            if (lowStockElement && parseInt(lowStockElement.textContent) !== data.lowStockCount) {
                lowStockElement.textContent = data.lowStockCount;
                lowStockElement.style.transform = 'scale(1.1)';
                setTimeout(() => lowStockElement.style.transform = 'scale(1)', 300);
            }
            
            // Atualizar gráficos se necessário
            if (salesChart && data.salesChartData) {
                const currentLabels = JSON.stringify(salesChart.data.labels);
                const newLabels = JSON.stringify(data.salesChartData.labels);
                
                if (currentLabels !== newLabels) {
                    salesChart.data.labels = data.salesChartData.labels;
                    salesChart.data.datasets[0].data = data.salesChartData.salesData;
                    salesChart.data.datasets[1].data = data.salesChartData.expensesData;
                    salesChart.update('none');
                }
            }
            
            if (cashFlowChart && data.cashFlowChartData) {
                cashFlowChart.data.labels = data.cashFlowChartData.labels;
                cashFlowChart.data.datasets[0].data = data.cashFlowChartData.inflowsData;
                cashFlowChart.data.datasets[1].data = data.cashFlowChartData.outflowsData;
                cashFlowChart.data.datasets[2].data = data.cashFlowChartData.netFlowData;
                cashFlowChart.update('none');
            }
            
            // Alertas dinâmicos
            if (data.dynamicAlerts && data.dynamicAlerts.length > 0 && window.FDSMULTSERVICES?.Toast) {
                data.dynamicAlerts.forEach(alert => {
                    window.FDSMULTSERVICES.Toast.show(alert.message, alert.type);
                });
            }
        })
        .catch(error => {
            console.warn('⚠️ Erro ao atualizar métricas:', error);
        });
    }

    // Função auxiliar para atualizar cards
    function updateMetricCard(idPrefix, value, percent, direction, icon) {
        const valueElement = document.getElementById(idPrefix);
        const changeElement = document.getElementById(idPrefix + '-change');
        
        if (!valueElement || !changeElement) return;

        const newValueFormatted = formatCurrency(value, false);
        
        if (valueElement.textContent !== newValueFormatted) {
            valueElement.classList.add('skeleton-loading');
            
            setTimeout(() => {
                valueElement.textContent = newValueFormatted;
                
                const percentElement = document.getElementById(idPrefix + '-change-percent');
                const iconElement = changeElement.querySelector('i');
                
                if (percentElement) percentElement.textContent = percent;
                if (iconElement) iconElement.className = 'fas ' + icon;
                
                changeElement.className = 'metric-change ' + direction;
                
                valueElement.classList.remove('skeleton-loading');
                valueElement.style.transform = 'scale(1.05)';
                setTimeout(() => valueElement.style.transform = 'scale(1)', 300);
            }, 500);
        }
    }

    // Função auxiliar de formatação
    function formatCurrency(value, usePrefix = true) {
        const formatted = (value || 0).toLocaleString('pt-PT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return usePrefix ? 'MT ' + formatted : formatted;
    }

    // Função para atualizar período do gráfico
    window.updateChartPeriod = function(days) {
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        console.log(`Atualizando gráfico para ${days} dias`);
    };

    // Inicialização
    updateGreeting();
    updateClock();
    
    setTimeout(() => {
        initSalesChart();
        initCashFlowChart();
    }, 100);
    
    // Atualizar relógio a cada minuto
    setInterval(updateClock, 60000);
    
    // Atualizar métricas a cada 30 segundos
    chartUpdateInterval = setInterval(updateDashboardMetrics, 30000);
    
    // Primeira atualização após 5 segundos
    setTimeout(updateDashboardMetrics, 5000);

    // Pausar atualizações quando a aba não está visível
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            if (chartUpdateInterval) clearInterval(chartUpdateInterval);
        } else {
            updateDashboardMetrics();
            chartUpdateInterval = setInterval(updateDashboardMetrics, 30000);
        }
    });

    console.log('✅ Dashboard inicializado com sucesso!');
});

// Limpeza ao sair da página
window.addEventListener('beforeunload', function() {
    if (chartUpdateInterval) clearInterval(chartUpdateInterval);
    if (salesChart) salesChart.destroy();
    if (cashFlowChart) cashFlowChart.destroy();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/dashboard/index.blade.php ENDPATH**/ ?>