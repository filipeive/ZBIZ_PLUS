



<?php ($setErrorsBag($errors ?? null)); ?>



<div class="<?php echo e($makeFormGroupClass()); ?>">

    
    <?php if(isset($label)): ?>
        <label for="<?php echo e($id); ?>" <?php if(isset($labelClass)): ?> class="<?php echo e($labelClass); ?>" <?php endif; ?>>
            <?php echo e($label); ?>

        </label>
    <?php endif; ?>

    
    <input type="file" id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>>

    
    <?php if($isInvalid()): ?>
        <span class="<?php echo e($makeInvalidFeedbackClass()); ?>" role="alert">
            <strong><?php echo e($errors->first($errorKey)); ?></strong>
        </span>
    <?php endif; ?>

</div>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {

        // Initialize the plugin.

        $('#<?php echo e($id); ?>').fileinput( <?php echo json_encode($config, 15, 512) ?> );

        // Workaround to force setup of invalid class.

        <?php if($isInvalid()): ?>
            $('#<?php echo e($id); ?>').closest('.file-input')
                .find('.file-caption-name')
                .addClass('is-invalid')

            $('#<?php echo e($id); ?>').closest('.file-input')
                .find('.file-preview')
                .css('box-shadow', '0 .15rem 0.25rem rgba(255,0,0,.25)');
        <?php endif; ?>

        // Make custom style for particular scenarios (modes).

        <?php if($presetMode == 'avatar'): ?>
            $('#<?php echo e($id); ?>').closest('.file-input')
                .addClass('text-center')
                .find('.file-drop-zone')
                .addClass('border-0');
        <?php endif; ?>
    })

</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/input-file-krajee.blade.php ENDPATH**/ ?>