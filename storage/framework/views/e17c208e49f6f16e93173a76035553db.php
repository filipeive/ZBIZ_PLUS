<div <?php echo e($attributes->merge(['class' => $makeModalClass(), 'id' => $id])); ?>

     <?php if(isset($staticBackdrop)): ?> data-backdrop="static" data-keyboard="false" <?php endif; ?>>

    <div class="<?php echo e($makeModalDialogClass()); ?>">
    <div class="modal-content">

        
        <div class="<?php echo e($makeModalHeaderClass()); ?>">
            <h4 class="modal-title">
                <?php if(isset($icon)): ?><i class="<?php echo e($icon); ?> mr-2"></i><?php endif; ?>
                <?php if(isset($title)): ?><?php echo e($title); ?><?php endif; ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        
        <?php if(! $slot->isEmpty()): ?>
            <div class="modal-body"><?php echo e($slot); ?></div>
        <?php endif; ?>

        
        <div class="modal-footer">
            <?php if(isset($footerSlot)): ?>
                <?php echo e($footerSlot); ?>

            <?php else: ?>
                <?php if (isset($component)) { $__componentOriginal84b78d66d5203b43b9d8c22236838526 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal84b78d66d5203b43b9d8c22236838526 = $attributes; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Form\Button::resolve(['label' => 'Close'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('adminlte-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\JeroenNoten\LaravelAdminLte\View\Components\Form\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => ''.e($makeCloseButtonClass).'','data-dismiss' => 'modal']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal84b78d66d5203b43b9d8c22236838526)): ?>
<?php $attributes = $__attributesOriginal84b78d66d5203b43b9d8c22236838526; ?>
<?php unset($__attributesOriginal84b78d66d5203b43b9d8c22236838526); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal84b78d66d5203b43b9d8c22236838526)): ?>
<?php $component = $__componentOriginal84b78d66d5203b43b9d8c22236838526; ?>
<?php unset($__componentOriginal84b78d66d5203b43b9d8c22236838526); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>

    </div>
    </div>

</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/tool/modal.blade.php ENDPATH**/ ?>