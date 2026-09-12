<?php $sidebarItemHelper = app('JeroenNoten\LaravelAdminLte\Helpers\SidebarItemHelper'); ?>

<?php if($sidebarItemHelper->isHeader($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.sidebar.menu-item-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php elseif($sidebarItemHelper->isLegacySearch($item) || $sidebarItemHelper->isCustomSearch($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.sidebar.menu-item-search-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php elseif($sidebarItemHelper->isMenuSearch($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.sidebar.menu-item-search-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php elseif($sidebarItemHelper->isSubmenu($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.sidebar.menu-item-treeview-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php elseif($sidebarItemHelper->isLink($item)): ?>

    
    <?php echo $__env->make('adminlte::partials.sidebar.menu-item-link', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php endif; ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/sidebar/menu-item.blade.php ENDPATH**/ ?>