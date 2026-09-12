<!-- Informações do Cliente -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-primary mb-0">
            <i class="fas fa-user me-2"></i>Informações do Cliente
        </h6>
        <span class="badge <?php echo e($order->status_badge); ?> fs-6"><?php echo e($order->status_text); ?></span>
    </div>
    
    <div class="row g-3">
        <div class="col-md-6">
            <strong class="text-dark"><?php echo e($order->customer_name); ?></strong>
            <?php if($order->customer_phone): ?>
                <div class="text-muted small mt-1">
                    <i class="fas fa-phone me-1"></i> <?php echo e($order->customer_phone); ?>

                </div>
            <?php endif; ?>
            <?php if($order->customer_email): ?>
                <div class="text-muted small mt-1">
                    <i class="fas fa-envelope me-1"></i> <?php echo e($order->customer_email); ?>

                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <div class="text-end">
                <div class="fw-bold text-primary fs-5">Pedido #<?php echo e($order->id); ?></div>
                <div class="text-muted small">
                    <i class="fas fa-calendar me-1"></i>
                    <?php echo e($order->created_at->format('d/m/Y H:i')); ?>

                </div>
                <?php if($order->delivery_date): ?>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-clock me-1"></i>
                        Entrega: <?php echo e($order->delivery_date->format('d/m/Y')); ?>

                        <?php if($order->isOverdue()): ?>
                            <span class="badge bg-danger ms-1">Atrasado</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Descrição e Prioridade -->
<div class="mb-4">
    <h6 class="text-primary mb-2">
        <i class="fas fa-info-circle me-2"></i>Descrição do Pedido
    </h6>
    <div class="bg-light p-3 rounded">
        <p class="mb-2"><?php echo e($order->description); ?></p>
        <div class="d-flex justify-content-between align-items-center">
            <span class="badge <?php echo e($order->priority_badge); ?>">
                <?php echo e($order->priority_text); ?>

            </span>
            <?php if($order->user): ?>
                <small class="text-muted">
                    <i class="fas fa-user-circle me-1"></i>
                    Criado por: <?php echo e($order->user->name); ?>

                </small>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Itens do Pedido -->
<div class="mb-4">
    <h6 class="text-primary mb-3">
        <i class="fas fa-box me-2"></i>Itens do Pedido (<?php echo e($order->items->count()); ?>)
    </h6>
    
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th class="text-center" style="width: 70px;">Qtd</th>
                    <th class="text-end" style="width: 100px;">Preço</th>
                    <th class="text-end" style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?php echo e($item->item_name); ?></div>
                            <?php if($item->product): ?>
                                <small class="text-success">
                                    <i class="fas fa-tag me-1"></i><?php echo e($item->product->name); ?>

                                </small>
                            <?php endif; ?>
                            <?php if($item->description): ?>
                                <div class="text-muted small mt-1"><?php echo e($item->description); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?php echo e($item->quantity); ?></span>
                        </td>
                        <td class="text-end">
                            <span class="text-muted">MT</span> <?php echo e(number_format($item->unit_price, 2, ',', '.')); ?>

                        </td>
                        <td class="text-end fw-semibold">
                            <span class="text-muted">MT</span> <?php echo e(number_format($item->total_price, 2, ',', '.')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                            <p>Nenhum item encontrado</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end fw-bold">Valor Total:</td>
                    <td class="text-end fw-bold text-primary">
                        MT <?php echo e(number_format($order->estimated_amount, 2, ',', '.')); ?>

                    </td>
                </tr>
                <?php if($order->advance_payment > 0): ?>
                    <tr>
                        <td colspan="3" class="text-end text-success">Sinal Recebido:</td>
                        <td class="text-end text-success">
                            MT <?php echo e(number_format($order->advance_payment, 2, ',', '.')); ?>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold text-danger">Valor Restante:</td>
                        <td class="text-end fw-bold text-danger">
                            MT <?php echo e(number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tfoot>
        </table>
    </div>
</div>

<!-- Observações -->
<?php if($order->notes || $order->internal_notes): ?>
    <div class="mb-4">
        <h6 class="text-primary mb-3">
            <i class="fas fa-sticky-note me-2"></i>Observações
        </h6>
        
        <?php if($order->notes): ?>
            <div class="bg-light p-3 rounded mb-3">
                <div class="fw-semibold text-dark mb-2">Observações do Cliente:</div>
                <p class="mb-0 text-muted"><?php echo e($order->notes); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if($order->internal_notes): ?>
            <div class="bg-info bg-opacity-10 border border-info p-3 rounded">
                <div class="fw-semibold text-info mb-2">
                    <i class="fas fa-lock me-1"></i>Notas Internas:
                </div>
                <p class="mb-0 text-secondary"><?php echo e($order->internal_notes); ?></p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Dívida Relacionada -->
<?php if($order->debt): ?>
    <div class="mb-4">
        <h6 class="text-primary mb-3">
            <i class="fas fa-credit-card me-2"></i>Dívida Relacionada
        </h6>
        
        <div class="bg-warning bg-opacity-10 border border-warning p-3 rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold">Status da Dívida:</span>
                <span class="badge <?php echo e($order->debt->status_badge); ?>">
                    <?php echo e($order->debt->status_text); ?>

                </span>
            </div>
            
            <div class="row g-2">
                <div class="col-md-6">
                    <small class="text-muted d-block">Valor Original:</small>
                    <span class="fw-bold">MT <?php echo e(number_format($order->debt->original_amount, 2, ',', '.')); ?></span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Valor Restante:</small>
                    <span class="fw-bold text-danger">MT <?php echo e(number_format($order->debt->remaining_amount, 2, ',', '.')); ?></span>
                </div>
            </div>
            
            <?php if($order->debt->due_date): ?>
                <div class="mt-2">
                    <small class="text-muted d-block">Vencimento:</small>
                    <span class="fw-semibold <?php echo e($order->debt->due_date < now() ? 'text-danger' : 'text-dark'); ?>">
                        <?php echo e($order->debt->due_date->format('d/m/Y')); ?>

                        <?php if($order->debt->due_date < now()): ?>
                            <i class="fas fa-exclamation-triangle ms-1"></i>
                        <?php endif; ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="<?php echo e(route('debts.show', $order->debt)); ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-external-link-alt me-1"></i>Ver Detalhes da Dívida
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Ações -->
<div class="mb-3">
    <h6 class="text-primary mb-3">
        <i class="fas fa-cogs me-2"></i>Ações Disponíveis
    </h6>
    
    <div class="d-grid gap-2">
        <?php if($order->canBeCompleted()): ?>
            <button type="button" class="btn btn-outline-primary btn-sm" 
                    onclick="openEditOrderOffcanvas(<?php echo e($order->id); ?>)">
                <i class="fas fa-edit me-2"></i>Editar Pedido
            </button>
        <?php endif; ?>
        
        <?php if($order->status !== 'cancelled'): ?>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                        data-bs-toggle="dropdown">
                    <i class="fas fa-exchange-alt me-2"></i>Alterar Status
                </button>
                <ul class="dropdown-menu">
                    <?php if($order->status !== 'pending'): ?>
                        <li>
                            <a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo e($order->id); ?>, 'pending')">
                                <i class="fas fa-clock me-2 text-warning"></i>Pendente
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($order->status !== 'in_progress'): ?>
                        <li>
                            <a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo e($order->id); ?>, 'in_progress')">
                                <i class="fas fa-cog me-2 text-info"></i>Em Andamento
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($order->status !== 'completed'): ?>
                        <li>
                            <a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo e($order->id); ?>, 'completed')">
                                <i class="fas fa-check me-2 text-success"></i>Concluído
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($order->status !== 'delivered'): ?>
                        <li>
                            <a class="dropdown-item" href="#" onclick="updateOrderStatus(<?php echo e($order->id); ?>, 'delivered')">
                                <i class="fas fa-truck me-2 text-primary"></i>Entregue
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if($order->canBeCancelled()): ?>
            <button type="button" class="btn btn-outline-danger btn-sm" 
                    onclick="cancelOrder(<?php echo e($order->id); ?>)">
                <i class="fas fa-times me-2"></i>Cancelar Pedido
            </button>
        <?php endif; ?>
        
        <?php if($order->canBeDelivered()): ?>
            <button type="button" class="btn btn-success btn-sm" 
                    onclick="convertToSale(<?php echo e($order->id); ?>)">
                <i class="fas fa-exchange-alt me-2"></i>Converter em Venda
            </button>
        <?php endif; ?>
    </div>
</div>

<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Confirmar alteração de status?')) {
        fetch(`/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Erro ao atualizar status', 'error');
            }
        })
        .catch(() => showToast('Erro de conexão', 'error'));
    }
}

function cancelOrder(orderId) {
    if (confirm('Tem certeza que deseja cancelar este pedido?')) {
        fetch(`/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(data.message || 'Erro ao cancelar pedido', 'error');
            }
        })
        .catch(() => showToast('Erro de conexão', 'error'));
    }
}

function convertToSale(orderId) {
    if (confirm('Converter este pedido em venda?')) {
        fetch(`/orders/${orderId}/convert-to-sale`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Pedido convertido em venda!', 'success');
                if (data.redirect) {
                    setTimeout(() => window.location.href = data.redirect, 1000);
                }
            } else {
                showToast(data.message || 'Erro ao converter pedido', 'error');
            }
        })
        .catch(() => showToast('Erro de conexão', 'error'));
    }
}
</script><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/orders/partials/details.blade.php ENDPATH**/ ?>