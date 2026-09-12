<?php $__env->startSection('title', 'Editar Movimento de Estoque'); ?>
<?php $__env->startSection('page-title', 'Editar Movimento de Estoque'); ?>
<?php
    $titleIcon = 'fas fa-edit';
?>

<?php $__env->startSection('breadcrumbs'); ?>
    <li class="breadcrumb-item">
        <a href="<?php echo e(route('stock-movements.index')); ?>">Movimentos de Estoque</a>
    </li>
    <li class="breadcrumb-item">
        <a href="<?php echo e(route('stock-movements.show', $stockMovement)); ?>">Movimento #<?php echo e($stockMovement->id); ?></a>
    </li>
    <li class="breadcrumb-item active">Editar</li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .movement-preview {
        background: var(--content-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .movement-icon {
        width: 80px;
        height: 80px;
        border-radius: var(--border-radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        transition: var(--transition);
    }

    .form-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--primary-blue);
        font-weight: 600;
    }

    .required-field::after {
        content: " *";
        color: var(--danger-red);
        font-weight: bold;
    }

    .movement-type-card {
        border: 2px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        background: var(--card-bg);
    }

    .movement-type-card:hover {
        border-color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .movement-type-card.selected {
        border-color: var(--primary-blue);
        background: rgba(91, 155, 213, 0.1);
    }

    .movement-type-card .type-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }

    .movement-type-card.in .type-icon {
        color: var(--success-green);
    }

    .movement-type-card.out .type-icon {
        color: var(--danger-red);
    }

    .movement-type-card.adjustment .type-icon {
        color: var(--warning-orange);
    }

    .warning-alert {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid var(--warning-orange);
        border-radius: var(--border-radius);
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Warning Alert -->
    <div class="warning-alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle text-warning me-3 fs-4"></i>
            <div>
                <h6 class="mb-1 text-warning">Atenção ao Editar Movimento</h6>
                <p class="mb-0 text-muted">
                    A alteração deste movimento pode afetar os cálculos de estoque. 
                    Certifique-se de que as informações estão corretas antes de salvar.
                </p>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('stock-movements.update', $stockMovement)); ?>" method="POST" id="movement-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informações do Produto -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-cube me-2 text-primary"></i>
                        Produto
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="product_id" class="form-label required-field">Produto/Serviço</label>
                            <select class="form-select <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="product_id" name="product_id" required>
                                <option value="">Selecione um produto ou serviço...</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>" 
                                            data-type="<?php echo e($product->type); ?>"
                                            <?php echo e((old('product_id', $stockMovement->product_id) == $product->id) ? 'selected' : ''); ?>>
                                        <?php echo e($product->name); ?> 
                                        <?php if($product->type === 'service'): ?>
                                            (Serviço)
                                        <?php else: ?>
                                            (Produto)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Movimento -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>
                        Tipo de Movimento
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="movement-type-card in <?php echo e(old('movement_type', $stockMovement->movement_type) == 'in' ? 'selected' : ''); ?>" data-type="in">
                                <div class="type-icon">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <h6 class="fw-bold">Entrada</h6>
                                <small class="text-muted">Recebimento de produtos</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="movement-type-card out <?php echo e(old('movement_type', $stockMovement->movement_type) == 'out' ? 'selected' : ''); ?>" data-type="out">
                                <div class="type-icon">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h6 class="fw-bold">Saída</h6>
                                <small class="text-muted">Venda ou consumo</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="movement-type-card adjustment <?php echo e(old('movement_type', $stockMovement->movement_type) == 'adjustment' ? 'selected' : ''); ?>" data-type="adjustment">
                                <div class="type-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <h6 class="fw-bold">Ajuste</h6>
                                <small class="text-muted">Correção de estoque</small>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="movement_type" id="movement_type" 
                           value="<?php echo e(old('movement_type', $stockMovement->movement_type)); ?>" required>
                    <?php $__errorArgs = ['movement_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger mt-2"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Detalhes do Movimento -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Detalhes do Movimento
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label required-field">Quantidade</label>
                            <input type="number" class="form-control <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="quantity" name="quantity" min="1" 
                                   value="<?php echo e(old('quantity', $stockMovement->quantity)); ?>" required>
                            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label for="movement_date" class="form-label required-field">Data do Movimento</label>
                            <input type="date" class="form-control <?php $__errorArgs = ['movement_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="movement_date" name="movement_date" 
                                   value="<?php echo e(old('movement_date', $stockMovement->movement_date->format('Y-m-d'))); ?>" required>
                            <?php $__errorArgs = ['movement_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label for="reason" class="form-label">Motivo/Observações</label>
                            <textarea class="form-control <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="reason" name="reason" rows="3" 
                                      placeholder="Descreva o motivo deste movimento (opcional)"><?php echo e(old('reason', $stockMovement->reason)); ?></textarea>
                            <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Informações de Auditoria -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-history me-2 text-primary"></i>
                        Histórico do Movimento
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Criado por</label>
                            <div class="form-control-plaintext">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 24px; height: 24px;">
                                        <small class="text-white fw-bold">
                                            <?php echo e(substr($stockMovement->user->name ?? 'S', 0, 1)); ?>

                                        </small>
                                    </div>
                                    <span><?php echo e($stockMovement->user->name ?? 'Sistema'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data de Criação</label>
                            <div class="form-control-plaintext">
                                <?php echo e($stockMovement->created_at->format('d/m/Y H:i')); ?>

                            </div>
                        </div>

                        <?php if($stockMovement->updated_at != $stockMovement->created_at): ?>
                            <div class="col-md-6">
                                <label class="form-label">Última Atualização</label>
                                <div class="form-control-plaintext">
                                    <?php echo e($stockMovement->updated_at->format('d/m/Y H:i')); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview do Movimento -->
                <div class="movement-preview" id="movement-preview">
                    <div class="text-center">
                        <div class="movement-icon" id="preview-icon">
                            <?php switch($stockMovement->movement_type):
                                case ('in'): ?>
                                    <i class="fas fa-arrow-up text-white fs-2"></i>
                                <?php break; ?>
                                <?php case ('out'): ?>
                                    <i class="fas fa-arrow-down text-white fs-2"></i>
                                <?php break; ?>
                                <?php case ('adjustment'): ?>
                                    <i class="fas fa-edit text-white fs-2"></i>
                                <?php break; ?>
                                <?php default: ?>
                                    <i class="fas fa-question-circle text-muted fs-2"></i>
                            <?php endswitch; ?>
                        </div>
                        <h6 id="preview-type">
                            <?php switch($stockMovement->movement_type):
                                case ('in'): ?>
                                    Entrada de Estoque
                                <?php break; ?>
                                <?php case ('out'): ?>
                                    Saída de Estoque
                                <?php break; ?>
                                <?php case ('adjustment'): ?>
                                    Ajuste de Estoque
                                <?php break; ?>
                                <?php default: ?>
                                    Selecione o tipo de movimento
                            <?php endswitch; ?>
                        </h6>
                        <p class="text-muted mb-3" id="preview-description">
                            <?php switch($stockMovement->movement_type):
                                case ('in'): ?>
                                    Aumentará o estoque disponível
                                <?php break; ?>
                                <?php case ('out'): ?>
                                    Diminuirá o estoque disponível
                                <?php break; ?>
                                <?php case ('adjustment'): ?>
                                    Correção manual do estoque
                                <?php break; ?>
                                <?php default: ?>
                                    Escolha um produto e tipo de movimento para ver o resumo
                            <?php endswitch; ?>
                        </p>
                        
                        <div class="row g-2 text-start" id="preview-details">
                            <div class="col-6">
                                <strong>Produto:</strong>
                            </div>
                            <div class="col-6" id="preview-product">
                                <?php echo e($stockMovement->product->name ?? 'Produto Removido'); ?>

                            </div>
                            <div class="col-6">
                                <strong>Tipo:</strong>
                            </div>
                            <div class="col-6" id="preview-movement-type">
                                <?php switch($stockMovement->movement_type):
                                    case ('in'): ?>
                                        <span class="badge badge-success">Entrada</span>
                                    <?php break; ?>
                                    <?php case ('out'): ?>
                                        <span class="badge badge-danger">Saída</span>
                                    <?php break; ?>
                                    <?php case ('adjustment'): ?>
                                        <span class="badge badge-warning">Ajuste</span>
                                    <?php break; ?>
                                <?php endswitch; ?>
                            </div>
                            <div class="col-6">
                                <strong>Quantidade:</strong>
                            </div>
                            <div class="col-6" id="preview-quantity">
                                <?php echo e($stockMovement->quantity); ?>

                            </div>
                            <div class="col-6">
                                <strong>Data:</strong>
                            </div>
                            <div class="col-6" id="preview-date">
                                <?php echo e($stockMovement->movement_date->format('d/m/Y')); ?>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Salvar Alterações
                    </button>
                    <a href="<?php echo e(route('stock-movements.show', $stockMovement)); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Ver Detalhes
                    </a>
                    <a href="<?php echo e(route('stock-movements.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
                    </a>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const movementTypeCards = document.querySelectorAll('.movement-type-card');
        const movementTypeInput = document.getElementById('movement_type');
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity');
        const dateInput = document.getElementById('movement_date');

        // Preview elements
        const previewIcon = document.getElementById('preview-icon');
        const previewType = document.getElementById('preview-type');
        const previewDescription = document.getElementById('preview-description');
        const previewProduct = document.getElementById('preview-product');
        const previewMovementType = document.getElementById('preview-movement-type');
        const previewQuantity = document.getElementById('preview-quantity');
        const previewDate = document.getElementById('preview-date');

        // Initialize preview with current values
        updatePreview();

        // Movement type selection
        movementTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                movementTypeCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');
                
                // Set hidden input value
                const type = this.dataset.type;
                movementTypeInput.value = type;
                
                updatePreview();
            });
        });

        // Update preview when inputs change
        [productSelect, quantityInput, dateInput].forEach(input => {
            input.addEventListener('change', updatePreview);
            input.addEventListener('input', updatePreview);
        });

        function updatePreview() {
            const selectedType = movementTypeInput.value;
            const selectedProduct = productSelect.options[productSelect.selectedIndex];
            const quantity = quantityInput.value;
            const date = dateInput.value;

            if (selectedType) {
                // Update icon and colors based on movement type
                switch (selectedType) {
                    case 'in':
                        previewIcon.innerHTML = '<i class="fas fa-arrow-up text-white fs-2"></i>';
                        previewIcon.style.background = 'linear-gradient(45deg, var(--success-green), #22C55E)';
                        previewType.textContent = 'Entrada de Estoque';
                        previewDescription.textContent = 'Aumentará o estoque disponível';
                        previewMovementType.innerHTML = '<span class="badge badge-success">Entrada</span>';
                        break;
                    case 'out':
                        previewIcon.innerHTML = '<i class="fas fa-arrow-down text-white fs-2"></i>';
                        previewIcon.style.background = 'linear-gradient(45deg, var(--danger-red), #EF4444)';
                        previewType.textContent = 'Saída de Estoque';
                        previewDescription.textContent = 'Diminuirá o estoque disponível';
                        previewMovementType.innerHTML = '<span class="badge badge-danger">Saída</span>';
                        break;
                    case 'adjustment':
                        previewIcon.innerHTML = '<i class="fas fa-edit text-white fs-2"></i>';
                        previewIcon.style.background = 'linear-gradient(45deg, var(--warning-orange), #F59E0B)';
                        previewType.textContent = 'Ajuste de Estoque';
                        previewDescription.textContent = 'Correção manual do estoque';
                        previewMovementType.innerHTML = '<span class="badge badge-warning">Ajuste</span>';
                        break;
                }
            }

            // Update preview details
            previewProduct.textContent = selectedProduct.text || previewProduct.textContent;
            previewQuantity.textContent = quantity || previewQuantity.textContent;
            previewDate.textContent = date ? new Date(date).toLocaleDateString('pt-BR') : previewDate.textContent;
        }

        // Form validation
        const form = document.getElementById('movement-form');
        form.addEventListener('submit', function(e) {
            if (!movementTypeInput.value) {
                e.preventDefault();
                showToast('Por favor, selecione o tipo de movimento.', 'warning');
                return false;
            }

            if (confirm('Tem certeza que deseja salvar as alterações? Esta ação pode afetar os cálculos de estoque.')) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/stock_movements/edit.blade.php ENDPATH**/ ?>