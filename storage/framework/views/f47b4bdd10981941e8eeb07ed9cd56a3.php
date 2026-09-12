<div <?php echo e($attributes->merge(['class' => "p-0 col-{$size}"])); ?>>

    <span class="nav-link">

        
        <?php if(isset($icon)): ?>
            <i class="<?php echo e($icon); ?>"></i>
        <?php endif; ?>

        
        <?php if(isset($title)): ?>
            <?php if(! empty($url) && $urlTarget === 'title'): ?>
                <a href="<?php echo e($url); ?>"><?php echo e($title); ?></a>
            <?php else: ?>
                <?php echo e($title); ?>

            <?php endif; ?>
        <?php endif; ?>

        
        <?php if(isset($text)): ?>
            <span class="<?php echo e($makeTextWrapperClass()); ?>">
                <?php if(! empty($url) && $urlTarget === 'text'): ?>
                    <a href="<?php echo e($url); ?>"><?php echo e($text); ?></a>
                <?php else: ?>
                    <?php echo e($text); ?>

                <?php endif; ?>
            </span>
        <?php endif; ?>

    </span>

</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/widget/profile-row-item.blade.php ENDPATH**/ ?>