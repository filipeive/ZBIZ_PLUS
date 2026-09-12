<?php $navbarItemHelper = app('JeroenNoten\LaravelAdminLte\Helpers\NavbarItemHelper'); ?>

<?php if($navbarItemHelper->isSubmenu($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.navbar.dropdown-item-submenu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php elseif($navbarItemHelper->isLink($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.navbar.dropdown-item-link', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php endif; ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/navbar/dropdown-item.blade.php ENDPATH**/ ?>