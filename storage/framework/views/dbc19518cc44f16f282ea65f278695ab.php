<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <input id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>>

<?php $__env->stopSection(true); ?>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {
        let usrCfg = <?php echo json_encode($config, 15, 512) ?>;

        // Check for disabled attribute (alternative to data-slider-enable).

        <?php if($attributes->has('disabled')): ?>
            usrCfg.enabled = false;
        <?php endif; ?>

        // Check for min, max and step attributes (alternatives to
        // data-slider-min, data-slider-max and data-slider-step).

        <?php if($attributes->has('min')): ?>
            usrCfg.min = Number( <?php echo json_encode($attributes['min'], 15, 512) ?> );
        <?php endif; ?>

        <?php if($attributes->has('max')): ?>
            usrCfg.max = Number( <?php echo json_encode($attributes['max'], 15, 512) ?> );
        <?php endif; ?>

        <?php if($attributes->has('step')): ?>
            usrCfg.step = Number( <?php echo json_encode($attributes['step'], 15, 512) ?> );
        <?php endif; ?>

        // Check for value attribute (alternative to data-slider-value).
        // Also, add support to auto select the previous submitted value.

        <?php if($attributes->has('value') || ($errors->any() && $enableOldSupport)): ?>

            let value = <?php echo json_encode($getOldValue($errorKey, $attributes['value']), 512) ?>;

            if (value) {
                value = value.split(",").map(Number);
                usrCfg.value = value.length > 1 ? value : value[0];
            }

        <?php endif; ?>

        // Initialize the plugin.

        let slider = $('#<?php echo e($id); ?>').bootstrapSlider(usrCfg);

        // Fix height conflict when orientation is vertical.

        let or = slider.bootstrapSlider('getAttribute', 'orientation');

        if (or == 'vertical') {
            $('#' + usrCfg.id).css('height', '210px');
            slider.bootstrapSlider('relayout');
        }
    })

</script>
<?php $__env->stopPush(); ?>




<?php $__env->startPush('css'); ?>
<style type="text/css">

    

    <?php if(isset($color)): ?>

        #<?php echo e($config['id']); ?> .slider-handle {
            background: <?php echo e($color); ?>;
        }
        #<?php echo e($config['id']); ?> .slider-selection {
            background: <?php echo e($color); ?>;
            opacity: 0.5;
        }
        #<?php echo e($config['id']); ?> .slider-tick.in-selection {
            background: <?php echo e($color); ?>;
            opacity: 0.9;
        }

    <?php endif; ?>

    

    <?php if(isset($appendSlot) || isset($prependSlot)): ?>

        #<?php echo e($config['id']); ?> {
            flex: 1 1 0;
            align-self: center;
            <?php if(isset($appendSlot)): ?> margin-right: 5px; <?php endif; ?>
            <?php if(isset($prependSlot)): ?> margin-left: 5px; <?php endif; ?>
        }

    <?php endif; ?>

</style>
<?php $__env->stopPush(); ?>




<?php if (! $__env->hasRenderedOnce('214e1563-536e-4644-9130-695ba1d2e1ba')): $__env->markAsRenderedOnce('214e1563-536e-4644-9130-695ba1d2e1ba'); ?>
<?php $__env->startPush('css'); ?>
<style type="text/css">

    .adminlte-invalid-islgroup .slider-track,
    .adminlte-invalid-islgroup > .input-group-prepend > *,
    .adminlte-invalid-islgroup > .input-group-append > * {
        box-shadow: 0 .25rem 0.5rem rgba(255,0,0,.25);
    }

    .adminlte-invalid-islgroup .slider-vertical {
        margin-bottom: 1rem;
    }

</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/input-slider.blade.php ENDPATH**/ ?>