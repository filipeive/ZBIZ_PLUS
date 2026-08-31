<?php $__env->startSection('title', 'Pedidos'); ?>
<?php $__env->startSection('page-title', 'Gestão de Pedidos'); ?>
<?php $__env->startSection('title-icon', 'fa-clipboard-list'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <li class="breadcrumb-item active">Pedidos</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-clipboard-list me-2"></i> Gestão de Pedidos
            </h2>
            <p class="text-muted mb-0">Controle e acompanhamento de pedidos da reprografia</p>
        </div>
        <div class="d-flex gap-2">
            
            <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Novo Pedido
            </a>
            <a href="<?php echo e(route('orders.report')); ?>" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i> Relatório
            </a>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Pendentes</h6>
                            <h3 class="mb-0 text-primary fw-bold"><?php echo e($stats['pending']); ?></h3>
                            <small class="text-muted">em espera</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Em Andamento</h6>
                            <h3 class="mb-0 text-cyan fw-bold"><?php echo e($stats['in_progress']); ?></h3>
                            <small class="text-muted">em produção</small>
                        </div>
                        <div class="text-cyan">
                            <i class="fas fa-cog fa-spin fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Concluídos</h6>
                            <h3 class="mb-0 text-success fw-bold"><?php echo e($stats['completed']); ?></h3>
                            <small class="text-muted">finalizados</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Atrasados</h6>
                            <h3 class="mb-0 text-warning fw-bold"><?php echo e($stats['overdue']); ?></h3>
                            <small class="text-muted">fora do prazo</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Pedidos
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('orders.index')); ?>" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pendente
                            </option>
                            <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>Em
                                Andamento</option>
                            <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Concluído
                            </option>
                            <option value="delivered" <?php echo e(request('status') === 'delivered' ? 'selected' : ''); ?>>Entregue
                            </option>
                            <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelado
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Prioridade</label>
                        <select class="form-select" name="priority">
                            <option value="">Todas</option>
                            <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Baixa</option>
                            <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Média</option>
                            <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>Alta</option>
                            <option value="urgent" <?php echo e(request('priority') === 'urgent' ? 'selected' : ''); ?>>Urgente
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Cliente</label>
                        <input type="text" class="form-control" name="customer" placeholder="Nome..."
                            value="<?php echo e(request('customer')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Início</label>
                        <input type="date" class="form-control" name="date_from" value="<?php echo e(request('date_from')); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Fim</label>
                        <input type="date" class="form-control" name="date_to" value="<?php echo e(request('date_to')); ?>">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Lista de Pedidos -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor Est.</th>
                            <th>Entrega</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                
                                <td><strong class="text-primary">#<?php echo e($order->id); ?></strong></td>
                                <td><?php echo e($order->customer_name); ?></td>
                                <td><?php echo e(Str::limit($order->description, 50)); ?></td>
                                <td class="text-end">MT <?php echo e(number_format($order->estimated_amount, 2, ',', '.')); ?></td>
                                <td><?php echo e($order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-'); ?></td>
                                <td><span class="badge <?php echo e($order->priority_badge); ?>"><?php echo e($order->priority_text); ?></span>
                                </td>
                                <td><span class="badge <?php echo e($order->status_badge); ?>"><?php echo e($order->status_text); ?></span></td>

                                
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('orders.show', $order)); ?>" class="btn btn-outline-info"
                                            title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <?php if($order->status === 'completed'): ?>
                                            <button type="button" class="btn btn-outline-primary" title="Criar Venda"
                                                data-bs-toggle="modal"
                                                data-bs-target="#convertToSaleModal<?php echo e($order->id); ?>">
                                                <i class="fas fa-cash-register"></i>
                                            </button>

                                            <button type="button" class="btn btn-outline-secondary" title="Criar Dívida"
                                                data-bs-toggle="modal"
                                                data-bs-target="#convertToDebtModal<?php echo e($order->id); ?>">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        <?php endif; ?>

                                        <?php if($order->canBeEdited()): ?>
                                            <a href="<?php echo e(route('orders.edit', $order)); ?>" class="btn btn-outline-warning"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($order->canBeCompleted()): ?>
                                            <form action="<?php echo e(route('orders.update-status', $order)); ?>" method="POST"
                                                class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-outline-success"
                                                    title="Concluir Pedido"
                                                    onclick="return confirm('Marcar pedido como concluído?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if($order->canBeCancelled()): ?>
                                            <form action="<?php echo e(route('orders.destroy', $order)); ?>" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Tem a certeza que deseja cancelar este pedido?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-outline-danger" title="Cancelar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            
                            
                            <?php if($order->status === 'completed'): ?>
                                
                                <div class="modal fade" id="convertToSaleModal<?php echo e($order->id); ?>" tabindex="-1"
                                    aria-labelledby="convertToSaleLabel<?php echo e($order->id); ?>" aria-hidden="true"
                                    data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="convertToSaleLabel<?php echo e($order->id); ?>">
                                                    <i class="fas fa-exchange-alt me-2"></i>
                                                    Converter Pedido #<?php echo e($order->id); ?> em Venda
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?php echo e(route('orders.convert-to-sale', $order)); ?>" method="POST"
                                                id="formConvertSale<?php echo e($order->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="modal-body">
                                                    <div class="alert alert-info mb-3">
                                                        <strong>Cliente:</strong> <?php echo e($order->customer_name); ?>

                                                        <br>
                                                        <strong>Pedido:</strong> #<?php echo e($order->id); ?>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Método de Pagamento *</label>
                                                        <select class="form-select" name="payment_method" required>
                                                            <option value="">Selecione...</option>
                                                            <option value="cash">Dinheiro</option>
                                                            <option value="card">Cartão</option>
                                                            <option value="transfer">Transferência</option>
                                                            <option value="mpesa">M-Pesa</option>
                                                            <option value="emola">Emola</option>
                                                        </select>
                                                    </div>

                                                    <div class="alert alert-success mb-0">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <strong>Total da Venda:</strong> MT
                                                        <?php echo e(number_format($order->estimated_amount, 2, ',', '.')); ?>

                                                        <br>
                                                        <small><?php echo e($order->items->count()); ?> item(ns)</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-check me-1"></i> Converter para Venda
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="modal fade" id="convertToDebtModal<?php echo e($order->id); ?>" tabindex="-1"
                                    aria-labelledby="convertToDebtLabel<?php echo e($order->id); ?>" aria-hidden="true"
                                    data-bs-backdrop="static">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title" id="convertToDebtLabel<?php echo e($order->id); ?>">
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Criar Dívida - Pedido #<?php echo e($order->id); ?>

                                                </h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?php echo e(route('orders.create-debt', $order)); ?>" method="POST"
                                                id="formConvertDebt<?php echo e($order->id); ?>">
                                                <?php echo csrf_field(); ?>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning mb-3">
                                                        <strong>Cliente:</strong> <?php echo e($order->customer_name); ?>

                                                        <br>
                                                        <strong>Pedido:</strong> #<?php echo e($order->id); ?>

                                                        <?php if($order->customer_phone): ?>
                                                            <br><strong>Telefone:</strong> <?php echo e($order->customer_phone); ?>

                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="mb-3">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <small class="text-muted">Valor Total</small>
                                                                <div class="fw-bold">MT
                                                                    <?php echo e(number_format($order->estimated_amount, 2, ',', '.')); ?>

                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">Sinal Recebido</small>
                                                                <div class="fw-bold text-success">MT
                                                                    <?php echo e(number_format($order->advance_payment, 2, ',', '.')); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="text-center">
                                                            <small class="text-muted">Valor Restante</small>
                                                            <div class="fs-4 fw-bold text-warning">
                                                                MT
                                                                <?php echo e(number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.')); ?>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Data de Vencimento *</label>
                                                        <input type="date" class="form-control" name="due_date"
                                                            value="<?php echo e(now()->addDays(30)->format('Y-m-d')); ?>"
                                                            min="<?php echo e(now()->format('Y-m-d')); ?>" required>
                                                        <small class="text-muted">Padrão: 30 dias a partir de hoje</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Descrição (Opcional)</label>
                                                        <textarea class="form-control" name="description" rows="2">Valor restante do Pedido #<?php echo e($order->id); ?> - <?php echo e($order->description); ?></textarea>
                                                    </div>

                                                    <div class="alert alert-info mb-0">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <small>
                                                            Esta dívida incluirá <?php echo e($order->items->count()); ?> item(ns).
                                                            O stock será automaticamente movimentado.
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-warning text-dark">
                                                        <i class="fas fa-check me-1"></i> Criar Dívida
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p>Nenhum pedido encontrado.</p>
                                    <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Criar Primeiro Pedido
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($orders->hasPages()): ?>
                <div class="card-footer bg-light">
                    <?php echo e($orders->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card.primary {
            border-left-color: #1e3a8a;
        }

        .stats-card.info {
            border-left-color: #0891b2;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .text-cyan {
            color: #0891b2 !important;
        }

        /* Melhorias responsivas */
        @media (max-width: 768px) {

            #orderFormOffcanvas,
            #orderViewOffcanvas {
                width: 100% !important;
            }

            .stats-card {
                margin-bottom: 1rem;
            }

            .btn-group-sm .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* Toast container posicionamento */
        .toast-container {
            z-index: 9999;
        }

        /* Estilo para campos inválidos */
        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Animação suave para offcanvas */
        .offcanvas {
            transition: transform 0.3s ease-in-out;
        }

        /* Badges personalizados */
        .badge {
            font-size: 0.75em;
            font-weight: 500;
        }

        /* Botões de ação hover */
        .btn-outline-info:hover,
        .btn-outline-warning:hover,
        .btn-outline-primary:hover,
        .btn-outline-danger:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        /* Loading state para botões */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Scrollbar personalizada */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/orders/index.blade.php ENDPATH**/ ?>