<div x-data="toastManager()"
     @toast.window="addToast($event.detail)"
     class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="pointer-events-auto w-full rounded-2xl border bg-slate-900/95 p-4 shadow-2xl backdrop-blur-xl transition relative overflow-hidden"
             :class="{
                 'border-emerald-500/40 text-emerald-300 shadow-emerald-950/40': toast.type === 'success',
                 'border-rose-500/40 text-rose-300 shadow-rose-950/40': toast.type === 'error',
                 'border-amber-500/40 text-amber-300 shadow-amber-950/40': toast.type === 'warning',
                 'border-blue-500/40 text-blue-300 shadow-blue-950/40': toast.type === 'info'
             }">
             
            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 h-1 bg-current opacity-30 transition-all duration-100 ease-linear"
                 :style="`width: ${toast.progress}%`"></div>

            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl font-black text-sm"
                     :class="{
                         'bg-emerald-500/20 text-emerald-400': toast.type === 'success',
                         'bg-rose-500/20 text-rose-400': toast.type === 'error',
                         'bg-amber-500/20 text-amber-400': toast.type === 'warning',
                         'bg-blue-500/20 text-blue-400': toast.type === 'info'
                     }">
                    <i :class="toast.icon"></i>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pr-2">
                    <h4 class="text-xs font-bold text-white capitalize" x-text="toast.title || toast.type"></h4>
                    <p class="mt-0.5 text-xs opacity-90 leading-relaxed break-words" x-html="toast.message"></p>
                </div>

                <!-- Close Button -->
                <button type="button" @click="removeToast(toast.id)" class="text-slate-400 hover:text-white transition p-1">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        init() {
            <?php if(session('toast_message')): ?>
                this.addToast({
                    type: '<?php echo e(session('toast_type', 'info')); ?>',
                    message: <?php echo json_encode(session('toast_message')); ?>

                });
            <?php endif; ?>

            <?php if(session('success')): ?>
                this.addToast({ type: 'success', title: 'Sucesso', message: <?php echo json_encode(session('success')); ?> });
            <?php endif; ?>

            <?php if(session('error')): ?>
                this.addToast({ type: 'error', title: 'Erro', message: <?php echo json_encode(session('error')); ?> });
            <?php endif; ?>

            <?php if(session('warning')): ?>
                this.addToast({ type: 'warning', title: 'Atenção', message: <?php echo json_encode(session('warning')); ?> });
            <?php endif; ?>

            <?php if(session('info') || session('status')): ?>
                this.addToast({ type: 'info', title: 'Informação', message: <?php echo json_encode(session('info') ?? session('status')); ?> });
            <?php endif; ?>

            <?php if(isset($errors) && $errors->any()): ?>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    this.addToast({ type: 'error', title: 'Erro de Validação', message: <?php echo json_encode($error); ?> });
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        },
        addToast(detail) {
            const id = Date.now() + Math.random();
            const type = detail.type || 'info';
            const icons = {
                success: 'fa-solid fa-circle-check',
                error: 'fa-solid fa-circle-xmark',
                warning: 'fa-solid fa-triangle-exclamation',
                info: 'fa-solid fa-circle-info'
            };
            const duration = detail.duration || 5000;
            
            const toast = {
                id,
                type,
                title: detail.title || (type === 'success' ? 'Sucesso' : (type === 'error' ? 'Erro' : (type === 'warning' ? 'Atenção' : 'Informação'))),
                message: detail.message || '',
                icon: detail.icon || icons[type] || 'fa-solid fa-bell',
                visible: true,
                progress: 100
            };

            this.toasts.push(toast);

            const intervalTime = 50;
            const decrement = (intervalTime / duration) * 100;
            const progressTimer = setInterval(() => {
                const target = this.toasts.find(t => t.id === id);
                if (!target) {
                    clearInterval(progressTimer);
                    return;
                }
                target.progress -= decrement;
                if (target.progress <= 0) {
                    clearInterval(progressTimer);
                    this.removeToast(id);
                }
            }, intervalTime);
        },
        removeToast(id) {
            const target = this.toasts.find(t => t.id === id);
            if (target) {
                target.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    };
}

// Global JS Helper for invoking toasts from any script
window.toast = function(options, type = 'info', title = null) {
    if (typeof options === 'string') {
        options = { message: options, type: type, title: title };
    }
    window.dispatchEvent(new CustomEvent('toast', { detail: options }));
};
window.toast.success = (msg, title) => window.toast({ type: 'success', message: msg, title: title });
window.toast.error = (msg, title) => window.toast({ type: 'error', message: msg, title: title });
window.toast.warning = (msg, title) => window.toast({ type: 'warning', message: msg, title: title });
window.toast.info = (msg, title) => window.toast({ type: 'info', message: msg, title: title });

window.showToast = window.toast;
window.toastr = window.toast;

// Override browser native alert to use Toast instead
window.alert = function(message) {
    if (!message) return;
    window.toast({
        type: 'warning',
        title: 'Atenção',
        message: String(message)
    });
};
</script><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/partials/toasts.blade.php ENDPATH**/ ?>