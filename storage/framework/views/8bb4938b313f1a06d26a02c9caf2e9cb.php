<div <?php echo e($attributes->merge(['class' => $makeCalloutClass()])); ?>>

    
    <?php if(! empty($title) || ! empty($icon)): ?>
        <h5 <?php if(isset($titleClass)): ?> class="<?php echo e($titleClass); ?>" <?php endif; ?>>
            <?php if(isset($icon)): ?> <i class="<?php echo e($icon); ?> mr-2"></i> <?php endif; ?>
            <?php if(isset($title)): ?> <?php echo e($title); ?> <?php endif; ?>
        </h5>
    <?php endif; ?>

    
    <?php echo e($slot); ?>


</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/widget/callout.blade.php ENDPATH**/ ?>