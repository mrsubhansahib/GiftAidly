<?php $__env->startComponent('mail::message'); ?>



# Verify your email

Hi <?php echo e($user->name ?? 'there'); ?>,  
Thanks for signing up. Please confirm your email to activate your account.

<?php $__env->startComponent('mail::button', ['url' => $url]); ?>
Verify Email
<?php echo $__env->renderComponent(); ?>

This link will expire in 60 minutes. If you didn’t create an account, you can safely ignore this email.

Warm regards,  
**The <?php echo e(config('app.name')); ?> Team**
<?php echo $__env->renderComponent(); ?>
<?php /**PATH D:\Laravel\Softic-Era\Current Projects\PaperLess\resources\views\emails\user\verify-email.blade.php ENDPATH**/ ?>