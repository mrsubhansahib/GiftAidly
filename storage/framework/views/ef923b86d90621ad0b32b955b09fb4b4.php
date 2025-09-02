

<?php $__env->startSection('body-attribuet'); ?>
class="authentication-bg"
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="account-pages py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
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

                            <h4 class="fw-bold text-dark mb-2">Verify your email</h4>
                            <p class="text-muted mb-0">
                                We sent a verification link to <span class="fw-semibold"><?php echo e(auth()->user()->email); ?></span>.
                                Please click the link to activate your account.
                            </p>
                        </div>

                        
                        <div class="alert alert-info alert-icon mt-4 mb-0" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm rounded bg-info d-flex justify-content-center align-items-center fs-18 me-2 flex-shrink-0">
                                    <i class="bx bx-info-circle text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    Didn’t get the email? Check your spam folder or resend a new link.
                                </div>
                            </div>
                        </div>

                        
                        <div class="mt-4">
                            <form method="POST" action="<?php echo e(route('verification.send')); ?>" class="d-grid gap-2">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-dark btn-lg fw-medium">
                                    Resend Verification Email
                                </button>
                            </form>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-link text-decoration-none text-muted px-0">
                                        Logout
                                    </button>
                                </form>

                                
                                <a href="<?php echo e(route('any', 'profile')); ?>" class="text-decoration-none small text-muted">
                                    Change email address
                                </a>
                            </div>
                        </div>

                        
                        <div class="mt-4 text-muted small">
                            <span class="d-inline-block">The verification link expires after a short time for security.</span>
                        </div>
                    </div>
                </div>

                <p class="text-center mt-4 text-white text-opacity-50">
                    Already clicked the link?
                    <a href="<?php echo e(route('login')); ?>" class="text-decoration-none text-white fw-bold">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', ['subtitle' => 'Verify Email'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\PaperLess\resources\views\auth\verify-email.blade.php ENDPATH**/ ?>