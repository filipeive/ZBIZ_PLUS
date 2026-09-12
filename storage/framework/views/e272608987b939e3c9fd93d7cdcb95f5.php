<li <?php if(isset($item['id'])): ?> id="<?php echo e($item['id']); ?>" <?php endif; ?> class="dropdown-submenu dropdown-hover">

    
    <a class="dropdown-item dropdown-toggle <?php echo e($item['class']); ?>" href=""
       data-toggle="dropdown" <?php echo $item['data-compiled'] ?? ''; ?>>

        
        <?php if(isset($item['icon'])): ?>
            <i class="<?php echo e($item['icon'] ?? ''); ?> <?php echo e(isset($item['icon_color']) ? 'text-' . $item['icon_color'] : ''); ?>"></i>
        <?php endif; ?>

        
        <?php echo e($item['text']); ?>


        
        <?php if(isset($item['label'])): ?>
            <span class="badge badge-<?php echo e($item['label_color'] ?? 'primary'); ?>">
                <?php echo e($item['label']); ?>

            </span>
        <?php endif; ?>

    </a>

    
    <ul class="dropdown-menu border-0 shadow">
        <?php echo $__env->renderEach('adminlte::partials.navbar.dropdown-item', $item['submenu'], 'item'); ?>
    </ul>

</li>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/navbar/dropdown-item-submenu.blade.php ENDPATH**/ ?>