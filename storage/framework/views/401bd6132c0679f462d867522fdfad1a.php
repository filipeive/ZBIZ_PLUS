<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <input id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        value="<?php echo e($getOldValue($errorKey, $attributes->get('value'))); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>>

<?php $__env->stopSection(true); ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/input.blade.php ENDPATH**/ ?>