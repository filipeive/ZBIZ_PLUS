<li <?php if(isset($item['id'])): ?> id="<?php echo e($item['id']); ?>" <?php endif; ?> class="nav-item has-treeview <?php echo e($item['submenu_class']); ?>">

    
    <a class="nav-link <?php echo e($item['class']); ?> <?php if(isset($item['shift'])): ?> <?php echo e($item['shift']); ?> <?php endif; ?>"
       href="" <?php echo $item['data-compiled'] ?? ''; ?>>

        <i class="<?php echo e($item['icon'] ?? 'far fa-fw fa-circle'); ?> <?php echo e(isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''); ?>"></i>

        <p>
            <?php echo e($item['text']); ?>

            <i class="fas fa-angle-left right"></i>

            <?php if(isset($item['label'])): ?>
                <span class="badge badge-<?php echo e($item['label_color'] ?? 'primary'); ?> right">
                    <?php echo e($item['label']); ?>

                </span>
            <?php endif; ?>
        </p>

    </a>

    
    <ul class="nav nav-treeview">
        <?php echo $__env->renderEach('adminlte::partials.sidebar.menu-item', $item['submenu'], 'item'); ?>
    </ul>

</li>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/sidebar/menu-item-treeview-menu.blade.php ENDPATH**/ ?>