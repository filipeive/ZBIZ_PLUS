

<li class="<?php echo e($makeListItemClass()); ?>" id="<?php echo e($id); ?>">

    
    <a <?php if($enableDropdownMode): ?> href="" <?php endif; ?> <?php echo e($attributes->merge($makeAnchorDefaultAttrs())); ?>>

        
        <i class="<?php echo e($makeIconClass()); ?>"></i>

        
        <span class="<?php echo e($makeBadgeClass()); ?>"><?php echo e($badgeLabel); ?></span>

    </a>

    
    <?php if($enableDropdownMode): ?>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

            
            <div class="adminlte-dropdown-content"></div>

            
            <div class="dropdown-divider"></div>

            
            <a href="<?php echo e($attributes->get('href')); ?>" class="dropdown-item dropdown-footer">
                <?php if(isset($dropdownFooterLabel)): ?>
                    <?php echo e($dropdownFooterLabel); ?>

                <?php else: ?>
                    <i class="fas fa-lg fa-search-plus"></i>
                <?php endif; ?>
            </a>

        </div>

    <?php endif; ?>

</li>



<?php if(! is_null($makeUpdateUrl()) && $makeUpdatePeriod() > 0): ?>
<?php $__env->startPush('js'); ?>
<script>

    $(() => {

        // Method to get new notification data from the configured url.

        let updateNotification = (nLink) =>
        {
            // Make an ajax call to the configured url. The response should be
            // an object with the new data. The supported properties are:
            // 'label', 'label_color', 'icon_color' and 'dropdown'.

            $.ajax({
                url: "<?php echo e($makeUpdateUrl()); ?>"
            })
            .done((data) => {
                nLink.update(data);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
            });
        };

        // First load of the notification data.

        let nLink = new _AdminLTE_NavbarNotification("<?php echo e($id); ?>");
        updateNotification(nLink);

        // Periodically update the notification.

        setInterval(updateNotification, <?php echo e($makeUpdatePeriod()); ?>, nLink);
    })

</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>



<?php if (! $__env->hasRenderedOnce('0e719aa9-0a9b-4aee-9143-312e2971cb35')): $__env->markAsRenderedOnce('0e719aa9-0a9b-4aee-9143-312e2971cb35'); ?>
<?php $__env->startPush('js'); ?>
<script>

    class _AdminLTE_NavbarNotification {

        /**
         * Constructor.
         *
         * target: The id of the target notification link.
         */
        constructor(target)
        {
            this.target = target;
        }

        /**
         * Update the notification link.
         *
         * data: An object with the new data.
         */
        update(data)
        {
            // Check if target and data exists.

            let t = $(`li#${this.target}`);

            if (t.length <= 0 || ! data) {
                return;
            }

            let badge = t.find(".navbar-badge");
            let icon = t.find(".nav-link > i");
            let dropdown = t.find(".adminlte-dropdown-content");

            // Update the badge label.

            if (data.label && data.label > 0) {
                badge.html(data.label);
            } else {
                badge.empty();
            }

            // Update the badge color.

            if (data.label_color) {
                badge.removeClass((idx, classes) => {
                    return (classes.match(/(^|\s)badge-\S+/g) || []).join(' ');
                }).addClass(`badge-${data.label_color} badge-pill`);
            }

            // Update the icon color.

            if (data.icon_color) {
                icon.removeClass((idx, classes) => {
                    return (classes.match(/(^|\s)text-\S+/g) || []).join(' ');
                }).addClass(`text-${data.icon_color}`);
            }

            // Update the dropdown content.

            if (data.dropdown && dropdown.length > 0) {
                dropdown.html(data.dropdown);
            }
        }
    }

</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/components/layout/navbar-notification.blade.php ENDPATH**/ ?>