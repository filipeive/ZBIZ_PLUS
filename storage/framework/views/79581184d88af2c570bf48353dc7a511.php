<div <?php echo e($attributes->merge(['class' => $makeCardClass()])); ?>>

    
    <div class="<?php echo e($makeHeaderClass()); ?>" style="<?php echo e($makeHeaderStyle()); ?>">

        
        <div class="widget-user-image">
            <?php if(isset($img)): ?>
                <img class="img-circle elevation-2" src="<?php echo e($img); ?>" alt="User avatar: <?php echo e($name); ?>">
            <?php elseif($layoutType === 'modern'): ?>
                <div class="img-circle elevation-2 d-flex bg-dark" style="width:90px;height:90px;">
                    <i class="fa-3x <?php echo e($icon); ?> text-silver m-auto"></i>
                </div>
            <?php elseif($layoutType === 'classic'): ?>
                <div class="img-circle elevation-2 float-left d-flex bg-dark" style="width:65px;height:65px;">
                    <i class="fa-2x <?php echo e($icon); ?> text-silver m-auto"></i>
                </div>
            <?php endif; ?>
        </div>

        
        <?php if(isset($name)): ?>
            <h3 class="widget-user-username mb-0"><?php echo e($name); ?></h3>
        <?php endif; ?>

        
        <?php if(isset($desc)): ?>
            <h5 class="widget-user-desc"><?php echo e($desc); ?></h5>
        <?php endif; ?>

    </div>

    
    <?php if(! $slot->isEmpty()): ?>
        <div class="<?php echo e($makeFooterClass()); ?>">
            <div class="row"><?php echo e($slot); ?></div>
        </div>
    <?php endif; ?>

</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/widget/profile-widget.blade.php ENDPATH**/ ?>