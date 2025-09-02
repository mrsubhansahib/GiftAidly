<?php $__env->startSection('body-attribuet'); ?>
class="authentication-bg"
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="account-pages py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <div class="mx-auto mb-4 text-center auth-logo">
                                <a href="<?php echo e(route('any', 'index')); ?>" class="logo-dark">
                                    <img src="/images/logo-dark.png" height="32" alt="logo dark">
                                </a>

                                <a href="<?php echo e(route('any', 'index')); ?>" class="logo-light">
                                    <img src="/images/logo-light.png" height="28" alt="logo light">
                                </a>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Welcome Back!</h3>
                                <p class="text-muted">Sign in to your account to continue</p>
                        </div>
                        <form method="POST" action="<?php echo e(route('signin')); ?>" class="mt-4">

                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" placeholder="user@demo.com" value="<?php echo e(old('email')); ?>"
                                    placeholder="Enter your email">
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger text-sm mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="password" class="form-label">Password</label>
                                    <a href="<?php echo e(route('password.request')); ?>"
                                        class="text-decoration-none small text-muted">Forgot password?</a>
                                </div>
                                <input type="password" class="form-control" id="password" name="password" placeholder="password"
                                    placeholder="Enter your password">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" required class="form-check-input" id="remember-me">
                                <label class="form-check-label" for="remember-me">Remember me</label>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-dark btn-lg fw-medium" type="submit">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-4 text-white text-opacity-50">Don't have an account?
                    <a href="<?php echo e(route('signup')); ?>"
                        class="text-decoration-none text-white fw-bold">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.base', ['subtitle' => 'Sign In'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\PaperLess\resources\views/auth/signin.blade.php ENDPATH**/ ?>