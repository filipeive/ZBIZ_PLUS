<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <input type="checkbox" id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass(), 'value' => 'true'])); ?>>

<?php $__env->stopSection(true); ?>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {

        let usrCfg = <?php echo json_encode($config, 15, 512) ?>;
        $('#<?php echo e($id); ?>').bootstrapSwitch(usrCfg);

        // Workaround to ensure correct state setup on initialization.

        $('#<?php echo e($id); ?>').bootstrapSwitch('state', usrCfg.state ?? false);

        // Add support to auto select the previous submitted value in case of
        // validation errors.

        <?php if($errors->any() && $enableOldSupport): ?>
            let oldState = <?php echo json_encode((bool)$getOldValue($errorKey), 15, 512) ?>;
            $('#<?php echo e($id); ?>').bootstrapSwitch('state', oldState);
        <?php endif; ?>
    })

</script>
<?php $__env->stopPush(); ?>




<?php if (! $__env->hasRenderedOnce('c07600c6-299c-414f-abe0-e050d041b98d')): $__env->markAsRenderedOnce('c07600c6-299c-414f-abe0-e050d041b98d'); ?>
<?php $__env->startPush('css'); ?>
<style type="text/css">

    
    .input-group .bootstrap-switch-handle-on,
    .input-group .bootstrap-switch-handle-off {
        height: 2.25rem !important;
    }

    
    .input-group-lg .bootstrap-switch-handle-on,
    .input-group-lg .bootstrap-switch-handle-off {
        height: 2.875rem !important;
        font-size: 1.25rem !important;
    }

    
    .input-group-sm .bootstrap-switch-handle-on,
    .input-group-sm .bootstrap-switch-handle-off {
        height: 1.8125rem !important;
        font-size: .875rem !important;
    }

    

    .adminlte-invalid-iswgroup > .bootstrap-switch-wrapper,
    .adminlte-invalid-iswgroup > .input-group-prepend > *,
    .adminlte-invalid-iswgroup > .input-group-append > * {
        box-shadow: 0 .25rem 0.5rem rgba(255,0,0,.25);
    }

</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/input-switch.blade.php ENDPATH**/ ?>