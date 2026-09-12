<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>

<nav class="main-header navbar
    <?php echo e(config('adminlte.classes_topnav_nav', 'navbar-expand-md')); ?>

    <?php echo e(config('adminlte.classes_topnav', 'navbar-white navbar-light')); ?>">

    <div class="<?php echo e(config('adminlte.classes_topnav_container', 'container')); ?>">

        
        <?php if(config('adminlte.logo_img_xl')): ?>
            <?php echo $__env->make('adminlte::partials.common.brand-logo-xl', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('adminlte::partials.common.brand-logo-xs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>

        
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        
        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            
            <ul class="nav navbar-nav">
                
                <?php echo $__env->renderEach('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item'); ?>

                
                <?php echo $__env->yieldContent('content_top_nav_left'); ?>
            </ul>
        </div>

        
        <ul class="navbar-nav ml-auto order-1 order-md-3 navbar-no-expand">
            
            <?php echo $__env->yieldContent('content_top_nav_right'); ?>

            
            <?php echo $__env->renderEach('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item'); ?>

            
            <?php if(Auth::user()): ?>
                <?php if(config('adminlte.usermenu_enabled')): ?>
                    <?php echo $__env->make('adminlte::partials.navbar.menu-item-dropdown-user-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('adminlte::partials.navbar.menu-item-logout-link', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if($layoutHelper->isRightSidebarEnabled()): ?>
                <?php echo $__env->make('adminlte::partials.navbar.menu-item-right-sidebar-toggler', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endif; ?>
        </ul>

    </div>

</nav>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/navbar/navbar-layout-topnav.blade.php ENDPATH**/ ?>