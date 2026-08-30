<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Processar a mensagem de toast única do controller (padrão novo)
        <?php if(session('toast_message')): ?>
            FDSMULTSERVICES.Toast.show(
                "<?php echo e(session('toast_message')); ?>",
                "<?php echo e(session('toast_type', 'info')); ?>"
            );
        <?php endif; ?>

        // Processar mensagens antigas (fallback para compatibilidade)
        <?php if(session('success')): ?>
            FDSMULTSERVICES.Toast.show("<?php echo e(session('success')); ?>", 'success');
        <?php endif; ?>

        <?php if(session('error')): ?>
            FDSMULTSERVICES.Toast.show("<?php echo e(session('error')); ?>", 'error');
        <?php endif; ?>

        <?php if(session('warning')): ?>
            FDSMULTSERVICES.Toast.show("<?php echo e(session('warning')); ?>", 'warning');
        <?php endif; ?>

        <?php if(session('info')): ?>
            FDSMULTSERVICES.Toast.show("<?php echo e(session('info')); ?>", 'info');
        <?php endif; ?>

        // Ocultar alerts tradicionais Bootstrap
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    });
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/partials/toasts.blade.php ENDPATH**/ ?>