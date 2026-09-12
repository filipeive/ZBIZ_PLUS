<?php $__currentLoopData = config('adminlte.plugins'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pluginName => $plugin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    

    <?php
        $plugSection = View::getSection('plugins.' . ($plugin['name'] ?? $pluginName));
        $isPlugActive = $plugin['active']
            ? ! isset($plugSection) || $plugSection
            : ! empty($plugSection);
    ?>

    

    <?php if($isPlugActive): ?>
        <?php $__currentLoopData = $plugin['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            

            <?php
                if (! empty($file['asset'])) {
                    $file['location'] = asset($file['location']);
                }
            ?>

            

            <?php if($file['type'] == $type && $type == 'css'): ?>
                <link rel="stylesheet" href="<?php echo e($file['location']); ?>">
            <?php elseif($file['type'] == $type && $type == 'js'): ?>
                <script src="<?php echo e($file['location']); ?>" <?php if(! empty($file['defer'])): ?> defer <?php endif; ?>></script>
            <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/plugins.blade.php ENDPATH**/ ?>