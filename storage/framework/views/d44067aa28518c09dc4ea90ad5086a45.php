

<div class="table-responsive">

<table id="<?php echo e($id); ?>" style="width:100%" <?php echo e($attributes->merge(['class' => $makeTableClass()])); ?>>

    
    <thead <?php if(isset($headTheme)): ?> class="thead-<?php echo e($headTheme); ?>" <?php endif; ?>>
        <tr>
            <?php $__currentLoopData = $heads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $th): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th <?php if(isset($th['classes'])): ?> class="<?php echo e($th['classes']); ?>" <?php endif; ?>
                    <?php if(isset($th['width'])): ?> style="width:<?php echo e($th['width']); ?>%" <?php endif; ?>
                    <?php if(isset($th['no-export'])): ?> dt-no-export <?php endif; ?>>
                    <?php echo e(is_array($th) ? ($th['label'] ?? '') : $th); ?>

                </th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tr>
    </thead>

    
    <tbody><?php echo e($slot); ?></tbody>

    
    <?php if(isset($withFooter)): ?>
        <tfoot <?php if(isset($footerTheme)): ?> class="thead-<?php echo e($footerTheme); ?>" <?php endif; ?>>
            <tr>
                <?php $__currentLoopData = $heads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $th): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th><?php echo e(is_array($th) ? ($th['label'] ?? '') : $th); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </tfoot>
    <?php endif; ?>

</table>

</div>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {
        $('#<?php echo e($id); ?>').DataTable( <?php echo json_encode($config, 15, 512) ?> );
    })

</script>
<?php $__env->stopPush(); ?>



<?php if(isset($beautify)): ?>
    <?php $__env->startPush('css'); ?>
    <style type="text/css">
        #<?php echo e($id); ?> tr td,  #<?php echo e($id); ?> tr th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
    <?php $__env->stopPush(); ?>
<?php endif; ?>



<?php if(! empty($config['responsive'])): ?>
    <?php if (! $__env->hasRenderedOnce('57a3ff7e-3004-420a-9faf-4561fd7d3243')): $__env->markAsRenderedOnce('57a3ff7e-3004-420a-9faf-4561fd7d3243'); ?>
    <?php $__env->startPush('css'); ?>
    <style type="text/css">
        .dataTable .child .dtr-details {
            width: 100%;
        }
        .dataTable .child .dtr-data {
            float: right;
        }
    </style>
    <?php $__env->stopPush(); ?>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/tool/datatable.blade.php ENDPATH**/ ?>