<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <select id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>>
        <?php echo e($slot); ?>

    </select>

<?php $__env->stopSection(true); ?>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {
        $('#<?php echo e($id); ?>').select2( <?php echo json_encode($config, 15, 512) ?> );

        // Add support to auto select old submitted values in case of
        // validation errors.

        <?php if($errors->any() && $enableOldSupport): ?>

            let oldOptions = <?php echo json_encode(collect($getOldValue($errorKey)), 15, 512) ?>;

            $('#<?php echo e($id); ?> option').each(function()
            {
                let value = $(this).val() || $(this).text();
                $(this).prop('selected', oldOptions.includes(value));
            });

            $('#<?php echo e($id); ?>').trigger('change');

        <?php endif; ?>
    })

</script>
<?php $__env->stopPush(); ?>




<?php if (! $__env->hasRenderedOnce('41918b12-40fd-4728-8d44-cbd0eb6e4eef')): $__env->markAsRenderedOnce('41918b12-40fd-4728-8d44-cbd0eb6e4eef'); ?>
<?php $__env->startPush('css'); ?>
<style type="text/css">

    
    .input-group-sm .select2-selection--single {
        height: calc(1.8125rem + 2px) !important
    }
    .input-group-sm .select2-selection--single .select2-selection__rendered,
    .input-group-sm .select2-selection--single .select2-selection__placeholder {
        font-size: .875rem !important;
        line-height: 2.125;
    }
    .input-group-sm .select2-selection--multiple {
        min-height: calc(1.8125rem + 2px) !important
    }
    .input-group-sm .select2-selection--multiple .select2-selection__rendered {
        font-size: .875rem !important;
        line-height: normal;
    }

    
    .input-group-lg .select2-selection--single {
        height: calc(2.875rem + 2px) !important;
    }
    .input-group-lg .select2-selection--single .select2-selection__rendered,
    .input-group-lg .select2-selection--single .select2-selection__placeholder {
        font-size: 1.25rem !important;
        line-height: 2.25;
    }
    .input-group-lg .select2-selection--multiple {
        min-height: calc(2.875rem + 2px) !important
    }
    .input-group-lg .select2-selection--multiple .select2-selection__rendered {
        font-size: 1.25rem !important;
        line-height: 1.7;
    }

    
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #e9ecef;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-search__field {
        display: none;
    }

</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/select2.blade.php ENDPATH**/ ?>