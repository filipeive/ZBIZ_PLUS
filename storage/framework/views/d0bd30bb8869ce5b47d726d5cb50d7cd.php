<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <textarea id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>

    ><?php echo e($getOldValue($errorKey, $slot)); ?></textarea>

<?php $__env->stopSection(true); ?>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {
        let usrCfg = <?php echo json_encode($config, 15, 512) ?>;

        // Check for placeholder attribute.

        <?php if(isset($attributes['placeholder'])): ?>
            usrCfg['placeholder'] = "<?php echo e($attributes['placeholder']); ?>";
        <?php endif; ?>

        // Initialize the plugin.

        $('#<?php echo e($id); ?>').summernote(usrCfg);

        // Check for disabled attribute.

        <?php if(isset($attributes['disabled'])): ?>
            $('#<?php echo e($id); ?>').summernote('disable');
        <?php endif; ?>
    })

</script>
<?php $__env->stopPush(); ?>




<?php if (! $__env->hasRenderedOnce('6584cb59-143a-4dde-b650-d785a9b449e5')): $__env->markAsRenderedOnce('6584cb59-143a-4dde-b650-d785a9b449e5'); ?>
<?php $__env->startPush('css'); ?>
<style type="text/css">

    
    .input-group-sm .note-editor {
        font-size: .875rem;
        line-height: 1;
    }

    
    .input-group-lg .note-editor {
        font-size: 1.25rem;
        line-height: 1.5;
    }

    

    .adminlte-invalid-itegroup .note-editor {
        box-shadow: 0 .25rem 0.5rem rgba(0,0,0,.25);
        border-color: #dc3545 !important;
    }

</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/text-editor.blade.php ENDPATH**/ ?>