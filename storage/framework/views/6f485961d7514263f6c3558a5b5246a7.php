<li>

    <form class="form-inline my-2" action="<?php echo e($item['href']); ?>" method="<?php echo e($item['method']); ?>">
        <?php echo e(csrf_field()); ?>


        <div class="input-group">

            
            <input class="form-control form-control-sidebar" type="search"
                <?php if(isset($item['id'])): ?> id="<?php echo e($item['id']); ?>" <?php endif; ?>
                name="<?php echo e($item['input_name']); ?>"
                placeholder="<?php echo e($item['text']); ?>"
                aria-label="<?php echo e($item['text']); ?>">

            
            <div class="input-group-append">
                <button class="btn btn-sidebar" type="submit">
                    <i class="fas fa-fw fa-search"></i>
                </button>
            </div>

        </div>
    </form>

</li>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/sidebar/menu-item-search-form.blade.php ENDPATH**/ ?>