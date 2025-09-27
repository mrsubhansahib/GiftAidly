<?php $__env->startSection('body-attribuet'); ?>
    class="authentication-bg"
<?php $__env->stopSection(); ?>

<!-- style -->
<style>
.password-input-container {
    position: relative;
}
.password-toggle-icon {
    position: absolute;
    top: 50%;
    margin-top: -5px;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    transition: color 0.2s ease-in-out;
    z-index: 100;
}
.password-toggle-icon:hover {
    color: #343a40;
}
.form-control-password {
    padding-right: 45px !important;
}
</style>

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
                                <h4 class="fw-bold text-dark mb-2">Sign Up</h4>
                                <p class="text-muted">New to our platform? Sign up now! It only takes a minute.</p>
                            </div>

                            <form action="<?php echo e(route('signup')); ?>" class="mt-4" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label" for="example-name">Name</label>
                                    <input type="name" id="example-name" name="name" class="form-control" value="<?php echo e(old('name')); ?>"
                                        placeholder="Enter your name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="example-email">Email</label>
                                    <input type="email" id="example-email" name="email" value="<?php echo e(old('email')); ?>"
                                        class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
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
                                    <?php if(session('mail_warning')): ?>
                                        <div class="text-amber-600 text-sm mt-2">
                                            <?php echo e(session('mail_warning')); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Password with Eye Icon -->
                                <div class="mb-3">
                                    <label class="form-label" for="example-password">Password</label>
                                    <div class="password-input-container">
                                        <input type="password" id="example-password" name="password"
                                            class="form-control form-control-password <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            placeholder="Enter your password">
                                        <span class="password-toggle-icon" onclick="togglePassword('example-password', this)">
                                            <iconify-icon icon="mdi:eye-outline" width="24" height="24"></iconify-icon>
                                        </span>
                                    </div>
                                    <?php $__errorArgs = ['password'];
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

                                <!-- Confirm Password with Eye Icon -->
                                <div class="mb-3">
                                    <label class="form-label" for="example-password-confirm">Confirm Password</label>
                                    <div class="password-input-container">
                                        <input type="password" id="example-password-confirm" name="password_confirmation"
                                            class="form-control form-control-password" placeholder="Confirm your password">
                                        <span class="password-toggle-icon" onclick="togglePassword('example-password-confirm', this)">
                                            <iconify-icon icon="mdi:eye-outline" width="24" height="24"></iconify-icon>
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" required class="form-check-input" id="checkbox-signin">
                                        <label class="form-check-label" for="checkbox-signin">
                                            I accept Terms and Condition
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-1 text-center d-grid">
                                    <button style="background: linear-gradient(45deg, #1d43ab, #94740dff);" 
                                            class="btn btn-lg text-light fw-medium" type="submit">
                                        Sign Up
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-center mt-4 text-white text-opacity-50">I already have an account
                        <a href="<?php echo e(route('signin')); ?>" class="text-decoration-none text-white fw-bold">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function togglePassword(fieldId, iconContainer) {
    const field = document.getElementById(fieldId);
    const icon = iconContainer.querySelector("iconify-icon");
    if (field.type === "password") {
        field.type = "text";
        icon.setAttribute("icon", "mdi:eye-off-outline");
    } else {
        field.type = "password";
        icon.setAttribute("icon", "mdi:eye-outline");
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', ['subtitle' => 'Sign Up'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/auth/signup.blade.php ENDPATH**/ ?>