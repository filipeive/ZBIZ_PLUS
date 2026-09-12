<?php $__env->startSection('adminlte_css_pre'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
?>

<?php $__env->startSection('auth_header', __('adminlte::adminlte.login_message')); ?>

<?php $__env->startSection('auth_body'); ?>
    <form action="<?php echo e($loginUrl); ?>" method="post">
        <?php echo csrf_field(); ?>

        
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('adminlte::adminlte.email')); ?>" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope <?php echo e(config('adminlte.classes_auth_icon', '')); ?>"></span>
                </div>
            </div>

            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($message); ?></strong>
                </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                placeholder="<?php echo e(__('adminlte::adminlte.password')); ?>">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock <?php echo e(config('adminlte.classes_auth_icon', '')); ?>"></span>
                </div>
            </div>

            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" role="alert">
                    <strong><?php echo e($message); ?></strong>
                </span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary" title="<?php echo e(__('adminlte::adminlte.remember_me_hint')); ?>">
                    <input type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>

                    <label for="remember">
                        <?php echo e(__('adminlte::adminlte.remember_me')); ?>

                    </label>
                </div>
            </div>

            <div class="col-5">
                <button type=submit class="btn btn-block <?php echo e(config('adminlte.classes_auth_btn', 'btn-flat btn-primary')); ?>">
                    <span class="fas fa-sign-in-alt"></span>
                    <?php echo e(__('adminlte::adminlte.sign_in')); ?>

                </button>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('auth_footer'); ?>
    
    <?php if($passResetUrl): ?>
        <p class="my-0">
            <a href="<?php echo e($passResetUrl); ?>">
                <?php echo e(__('adminlte::adminlte.i_forgot_my_password')); ?>

            </a>
        </p>
    <?php endif; ?>

    
    <?php if($registerUrl): ?>
        <p class="my-0">
            <a href="<?php echo e($registerUrl); ?>">
                <?php echo e(__('adminlte::adminlte.register_a_new_membership')); ?>

            </a>
        </p>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::auth.auth-page', ['authType' => 'login'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/auth/login.blade.php ENDPATH**/ ?>