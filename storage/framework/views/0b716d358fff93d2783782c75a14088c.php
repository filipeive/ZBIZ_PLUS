<?php ($setErrorsBag($errors ?? null)); ?>



<?php $__env->startSection('input_group_item'); ?>

    
    <input id="<?php echo e($id); ?>" name="<?php echo e($name); ?>"
        <?php echo e($attributes->merge(['class' => $makeItemClass()])); ?>>

<?php $__env->stopSection(true); ?>



<?php $__env->startPush('js'); ?>
<script>

    $(() => {
        let usrCfg = _AdminLTE_DateRange.parseCfg( <?php echo json_encode($config, 15, 512) ?> );

        // Add support to display a placeholder. In this situation, the related
        // input won't be updated automatically and the cancel button will be
        // used to clear the input.

        <?php if($attributes->has('placeholder')): ?>

            usrCfg.autoUpdateInput = false;

            $('#<?php echo e($id); ?>').on('apply.daterangepicker', function(ev, picker)
            {
                let startDate = picker.startDate.format(picker.locale.format);
                let endDate = picker.endDate.format(picker.locale.format);

                let value = picker.singleDatePicker
                    ? startDate
                    : startDate + picker.locale.separator + endDate;

                $(this).val(value);
            });

            $('#<?php echo e($id); ?>').on('cancel.daterangepicker', function(ev, picker)
            {
                $(this).val('');
            });

        <?php endif; ?>

        // Check if the default set of ranges should be enabled, and if a
        // default range should be set at initialization.

        <?php if(isset($enableDefaultRanges)): ?>

            usrCfg.ranges = usrCfg.ranges || _AdminLTE_DateRange.defaultRanges;
            let range = usrCfg.ranges[ <?php echo json_encode($enableDefaultRanges, 15, 512) ?> ];

            if (Array.isArray(range) && range.length > 1) {
                usrCfg.startDate = range[0];
                usrCfg.endDate = range[1];
            }

        <?php endif; ?>

        // Add support to auto select the previous submitted value in case
        // of validation errors. Note the previous value may be a date range or
        // a single date depending on the plugin configuration.

        <?php if($errors->any() && $enableOldSupport): ?>

            let oldRange = <?php echo json_encode($getOldValue($errorKey, ""), 512) ?>;
            let separator = " - ";

            if (usrCfg.locale && usrCfg.locale.separator) {
                separator = usrCfg.locale.separator;
            }

            // Update the related input.

            if (! usrCfg.autoUpdateInput) {
                $('#<?php echo e($id); ?>').val(oldRange);
            }

            // Update the internal plugin data.

            if (oldRange) {
                oldRange = oldRange.split(separator);
                usrCfg.startDate = oldRange.length > 0 ? oldRange[0] : null;
                usrCfg.endDate = oldRange.length > 1 ? oldRange[1] : null;
            }

        <?php endif; ?>

        // Setup the underlying date range plugin.

        $('#<?php echo e($id); ?>').daterangepicker(usrCfg);
    })

</script>
<?php $__env->stopPush(); ?>



<?php if (! $__env->hasRenderedOnce('72104dfb-b2ea-4808-a0a5-e217a9f00aa1')): $__env->markAsRenderedOnce('72104dfb-b2ea-4808-a0a5-e217a9f00aa1'); ?>
<?php $__env->startPush('js'); ?>
<script>

    class _AdminLTE_DateRange {

        /**
         * A default set of ranges options.
         */
        static defaultRanges = {
            'Today': [
                moment().startOf('day'),
                moment().endOf('day')
            ],
            'Yesterday': [
                moment().subtract(1, 'days').startOf('day'),
                moment().subtract(1, 'days').endOf('day')
            ],
            'Last 7 Days': [
                moment().subtract(6, 'days'),
                moment()
            ],
            'Last 30 Days': [
                moment().subtract(29, 'days'),
                moment()
            ],
            'This Month': [
                moment().startOf('month'),
                moment().endOf('month')
            ],
            'Last Month': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        }

        /**
         * Parse the php plugin configuration and eval the javascript code.
         *
         * cfg: A json with the php side configuration.
         */
        static parseCfg(cfg)
        {
            for (const prop in cfg) {
                let v = cfg[prop];

                if (typeof v === 'string' && v.startsWith('js:')) {
                    cfg[prop] = eval(v.slice(3));
                } else if (typeof v === 'object') {
                    cfg[prop] = _AdminLTE_DateRange.parseCfg(v);
                }
            }

            return cfg;
        }
    }

</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('adminlte::components.form.input-group-component', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/form/date-range.blade.php ENDPATH**/ ?>