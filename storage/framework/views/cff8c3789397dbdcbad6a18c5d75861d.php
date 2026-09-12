<?php $preloaderHelper = app('JeroenNoten\LaravelAdminLte\Helpers\PreloaderHelper'); ?>

<div class="<?php echo e($preloaderHelper->makePreloaderClasses()); ?>" style="<?php echo e($preloaderHelper->makePreloaderStyle()); ?>">

    <?php if (! empty(trim($__env->yieldContent('preloader')))): ?>

        
        <?php echo $__env->yieldContent('preloader'); ?>

    <?php else: ?>

        
        <img src="<?php echo e(asset(config('adminlte.preloader.img.path', 'vendor/adminlte/dist/img/AdminLTELogo.png'))); ?>"
             class="img-circle <?php echo e(config('adminlte.preloader.img.effect', 'animation__shake')); ?>"
             alt="<?php echo e(config('adminlte.preloader.img.alt', 'AdminLTE Preloader Image')); ?>"
             width="<?php echo e(config('adminlte.preloader.img.width', 60)); ?>"
             height="<?php echo e(config('adminlte.preloader.img.height', 60)); ?>"
             style="animation-iteration-count:infinite;">

    <?php endif; ?>

</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/common/preloader.blade.php ENDPATH**/ ?>