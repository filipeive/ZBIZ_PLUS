
<?php if(isset($emptyOption)): ?>

    <option value>
        <?php echo e(is_string($emptyOption) ? $emptyOption : ''); ?>

    </option>


<?php elseif(isset($placeholder)): ?>

    <option class="d-none" value>
        <?php echo e(is_string($placeholder) ? $placeholder : ''); ?>

    </option>

<?php endif; ?>


<?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <option value="<?php echo e($key); ?>"
        <?php if($isSelected($key)): ?> selected <?php endif; ?>
        <?php if($isDisabled($key)): ?> disabled <?php endif; ?>>
        <?php echo e($value); ?>

    </option>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/options.blade.php ENDPATH**/ ?>