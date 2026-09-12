<div <?php echo e($attributes->merge(['class' => $makeBoxClass()])); ?>>

    
    <?php if(isset($icon)): ?>
        <span class="<?php echo e($makeIconClass()); ?>">
            <i class="<?php echo e($icon); ?>"></i>
        </span>
    <?php endif; ?>

    
    <div class="info-box-content">

        
        <?php if(isset($title)): ?>
            <span class="info-box-text">

                <?php if(isset($url) && $urlTarget == 'title'): ?>
                    <a class="info-box-url text-reset" href="<?php echo e($url); ?>">
                        <u><?php echo e($title); ?></u>
                    </a>
                <?php else: ?>
                    <?php echo e($title); ?>

                <?php endif; ?>

            </span>
        <?php endif; ?>

        
        <?php if(isset($text)): ?>
            <span class="info-box-number">

                <?php if(isset($url) && $urlTarget == 'text'): ?>
                    <a class="info-box-url text-reset" href="<?php echo e($url); ?>">
                        <u><?php echo e($text); ?></u>
                    </a>
                <?php else: ?>
                    <?php echo e($text); ?>

                <?php endif; ?>

            </span>
        <?php endif; ?>

        
        <?php if(isset($progress) && isset($attributes['id'])): ?>
            <?php if (isset($component)) { $__componentOriginalaba4b9eb83c712e891ab3871e2409604 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaba4b9eb83c712e891ab3871e2409604 = $attributes; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::resolve(['value' => ''.e($progress).'','theme' => ''.e($progressTheme).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('adminlte-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'progress-'.e($attributes['id']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaba4b9eb83c712e891ab3871e2409604)): ?>
<?php $attributes = $__attributesOriginalaba4b9eb83c712e891ab3871e2409604; ?>
<?php unset($__attributesOriginalaba4b9eb83c712e891ab3871e2409604); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaba4b9eb83c712e891ab3871e2409604)): ?>
<?php $component = $__componentOriginalaba4b9eb83c712e891ab3871e2409604; ?>
<?php unset($__componentOriginalaba4b9eb83c712e891ab3871e2409604); ?>
<?php endif; ?>
        <?php elseif(isset($progress)): ?>
            <?php if (isset($component)) { $__componentOriginalaba4b9eb83c712e891ab3871e2409604 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaba4b9eb83c712e891ab3871e2409604 = $attributes; } ?>
<?php $component = JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::resolve(['value' => ''.e($progress).'','theme' => ''.e($progressTheme).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('adminlte-progress'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\JeroenNoten\LaravelAdminLte\View\Components\Widget\Progress::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaba4b9eb83c712e891ab3871e2409604)): ?>
<?php $attributes = $__attributesOriginalaba4b9eb83c712e891ab3871e2409604; ?>
<?php unset($__attributesOriginalaba4b9eb83c712e891ab3871e2409604); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaba4b9eb83c712e891ab3871e2409604)): ?>
<?php $component = $__componentOriginalaba4b9eb83c712e891ab3871e2409604; ?>
<?php unset($__componentOriginalaba4b9eb83c712e891ab3871e2409604); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if(isset($description)): ?>
            <span class="progress-description"><?php echo e($description); ?></span>
        <?php endif; ?>

    </div>

</div>



<?php if (! $__env->hasRenderedOnce('579e8b14-23c3-4e82-bcec-cac1e6436496')): $__env->markAsRenderedOnce('579e8b14-23c3-4e82-bcec-cac1e6436496'); ?>
<?php $__env->startPush('js'); ?>
<script>

    class _AdminLTE_InfoBox {

        /**
         * Constructor.
         *
         * target: The id of the target info box.
         */
        constructor(target)
        {
            this.target = target;
        }

        /**
         * Update the info box.
         *
         * data: An object with the new data.
         */
        update(data)
        {
            // Check if target and data exists.

            let t = $(`#${this.target}`);

            if (t.length <= 0 || ! data) {
                return;
            }

            // Update available data.

            if (data.title) {
                t.find('.info-box-text').html(data.title);
            }

            if (data.text) {
                t.find('.info-box-number').html(data.text);
            }

            if (data.icon) {
                t.find('.info-box-icon i').attr('class', data.icon);
            }

            if (data.description) {
                t.find('.progress-description').html(data.description);
            }

            if (data.url) {
                t.find('.info-box-url').attr('href', data.url);
            }

            // Update progress bar.

            if (data.progress) {
                let pBar = new _AdminLTE_Progress(`progress-${this.target}`);
                pBar.setValue(data.progress);
            }
        }
    }

</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/widget/info-box.blade.php ENDPATH**/ ?>