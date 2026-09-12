<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <input id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>>

<?php $__env->stopSection(true); ?>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {

        // Create a method to set the addon color.

        let setAddonColor = function()
        {
            let color = $('#<?php echo e($id); ?>').data('colorpicker').getValue();

            $('#<?php echo e($id); ?>').closest('.input-group')
                .find('.input-group-text > i')
                .css('color', color);
        }

        // Init the plugin and register the change event listener.

        $('#<?php echo e($id); ?>').colorpicker( <?php echo json_encode($config, 15, 512) ?> )
            .on('change', setAddonColor);

        // Add support to auto select the previous submitted value in case
        // of validation errors.

        <?php if($errors->any() && $enableOldSupport): ?>
            let oldColor = <?php echo json_encode($getOldValue($errorKey, ""), 512) ?>;
            $('#<?php echo e($id); ?>').val(oldColor).change();
        <?php endif; ?>

        // Set the initial color for the addon.

        setAddonColor();
    })

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/input-color.blade.php ENDPATH**/ ?>