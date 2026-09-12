<?php $__env->startSection('adminlte_css'); ?>
    <?php echo $__env->yieldContent('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('classes_body', 'lockscreen'); ?>

<?php
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');
    $dashboardUrl = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home');

    if (config('adminlte.use_route_url', false)) {
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
        $dashboardUrl = $dashboardUrl ? route($dashboardUrl) : '';
    } else {
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
        $dashboardUrl = $dashboardUrl ? url($dashboardUrl) : '';
    }
?>

<?php $__env->startSection('body'); ?>
    <div class="lockscreen-wrapper">

        
        <div class="lockscreen-logo">
            <a href="<?php echo e($dashboardUrl); ?>">
                <img src="<?php echo e(asset(config('adminlte.logo_img'))); ?>" height="50">
                <?php echo config('adminlte.logo', '<b>Admin</b>LTE'); ?>

            </a>
        </div>

        
        <div class="lockscreen-name">
            <?php echo e(isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email); ?>

        </div>

        
        <div class="lockscreen-item">
            <?php if(config('adminlte.usermenu_image')): ?>
                <div class="lockscreen-image">
                    <img src="<?php echo e(Auth::user()->adminlte_image()); ?>" alt="<?php echo e(Auth::user()->name); ?>">
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('password.confirm')); ?>"
                class="lockscreen-credentials <?php if(! config('adminlte.usermenu_image')): ?> ml-0 <?php endif; ?>">
                <?php echo csrf_field(); ?>

                <div class="input-group">
                    <input id="password" type="password" name="password"
                        class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="<?php echo e(__('adminlte::adminlte.password')); ?>" required autofocus>

                    <div class="input-group-append">
                        <button type="submit" class="btn">
                            <i class="fas fa-arrow-right text-muted"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="lockscreen-subitem text-center" role="alert">
                <b class="text-danger"><?php echo e($message); ?></b>
            </div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        
        <div class="help-block text-center">
            <?php echo e(__('adminlte::adminlte.confirm_password_message')); ?>

        </div>

        
        <div class="text-center">
            <a href="<?php echo e($passResetUrl); ?>">
                <?php echo e(__('adminlte::adminlte.i_forgot_my_password')); ?>

            </a>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('adminlte_js'); ?>
    <?php echo $__env->yieldPushContent('js'); ?>
    <?php echo $__env->yieldContent('js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/auth/passwords/confirm.blade.php ENDPATH**/ ?>