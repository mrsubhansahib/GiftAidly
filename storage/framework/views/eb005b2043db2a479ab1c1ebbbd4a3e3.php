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
                                <?php
                                    use App\Models\User;
                                    $admin = User::where('role', 'admin')->first();
                                ?>
                                <h4 class="fw-bold text-dark mb-2">Hi ! <?php echo e($admin->name); ?></h4>
                                <p class="text-muted">Enter your password to access the admin.</p>
                            </div>

                            <form action="<?php echo e(route('admin.signin')); ?>" method="POST" class="mt-4">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label" for="example-password">Password</label>
                                    <input type="password" name="password" required id="example-password" class="form-control"
                                        placeholder="Enter your password">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" required class="form-check-input" id="checkbox-signin">
                                        <label class="form-check-label" for="checkbox-signin">I accept Terms and
                                            Condition</label>
                                    </div>
                                </div>

                                <div class="mb-1 text-center d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-center mt-4 text-white text-opacity-50">Not you? return
                        <a href="<?php echo e(route('second', ['auth', 'signup'])); ?>"
                            class="text-decoration-none text-white fw-bold">Sign Up</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', ['subtitle' => 'Lock Screen'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/auth/admin-login.blade.php ENDPATH**/ ?>