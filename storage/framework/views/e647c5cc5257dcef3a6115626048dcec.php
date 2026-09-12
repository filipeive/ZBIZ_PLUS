<?php ( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') ); ?>

<?php if(config('adminlte.use_route_url', false)): ?>
    <?php ( $logout_url = $logout_url ? route($logout_url) : '' ); ?>
<?php else: ?>
    <?php ( $logout_url = $logout_url ? url($logout_url) : '' ); ?>
<?php endif; ?>

<li class="nav-item">
    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fa fa-fw fa-power-off text-red"></i>
        <?php echo e(__('adminlte::adminlte.log_out')); ?>

    </a>
    <form id="logout-form" action="<?php echo e($logout_url); ?>" method="POST" style="display: none;">
        <?php if(config('adminlte.logout_method')): ?>
            <?php echo e(method_field(config('adminlte.logout_method'))); ?>

        <?php endif; ?>
        <?php echo e(csrf_field()); ?>

    </form>
</li><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/navbar/menu-item-logout-link.blade.php ENDPATH**/ ?>