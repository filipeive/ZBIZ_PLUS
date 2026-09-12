

<li class="nav-item adminlte-darkmode-widget">

    <a class="nav-link" href="#" role="button">
        <i class="<?php echo e($makeIconClass()); ?>"></i>
    </a>

</li>



<?php if (! $__env->hasRenderedOnce('eb9445ff-c8cc-4d0d-9684-c6faff55602d')): $__env->markAsRenderedOnce('eb9445ff-c8cc-4d0d-9684-c6faff55602d'); ?>
<?php $__env->startPush('js'); ?>
<script>

    $(() => {

        const body = document.querySelector('body');
        const widget = document.querySelector('li.adminlte-darkmode-widget');
        const widgetIcon = widget.querySelector('i');

        // Get the set of classes to be toggled on the widget icon.

        const iconClasses = [
            ...<?php echo json_encode($makeIconEnabledClass(), 15, 512) ?>,
            ...<?php echo json_encode($makeIconDisabledClass(), 15, 512) ?>
        ];

        // Add 'click' event listener for the darkmode widget.

        widget.addEventListener('click', () => {

            // Toggle dark-mode class on the main body tag.

            body.classList.toggle('dark-mode');

            // Support to IFrame mode: toggle dark-mode class on the body of
            // any present iframe.

            let iframes = document.querySelectorAll('div.iframe-mode iframe');

            iframes.forEach((f) => {
                b = f.contentDocument.querySelector('body');
                b.classList.toggle('dark-mode');
            });

            // Toggle the classes on the widget icon.

            iconClasses.forEach((c) => widgetIcon.classList.toggle(c));

            // Notify the server. The server will be in charge to persist
            // the dark mode configuration over multiple request.

            const fetchCfg = {
                headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
                method: 'POST',
            };

            fetch(
                "<?php echo e(route('adminlte.darkmode.toggle')); ?>",
                fetchCfg
            )
            .catch((error) => {
                console.log(
                    'Failed to notify server that dark mode was toggled',
                    error
                );
            });
        });
    })

</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/layout/navbar-darkmode-widget.blade.php ENDPATH**/ ?>