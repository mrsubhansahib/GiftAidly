<?php $__env->startComponent('mail::message'); ?>


# Reset your password

Hi <?php echo new \Illuminate\Support\EncodedHtmlString($user->name ?? 'there'); ?>,  
We received a request to reset your password.

<?php $__env->startComponent('mail::button', ['url' => $url]); ?>
Reset Password
<?php echo $__env->renderComponent(); ?>

This link expires in 60 minutes. If you didn’t request a reset, no action is required.

Stay secure,<br>
**The <?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?> Team**
<?php echo $__env->renderComponent(); ?>
<?php /**PATH D:\Laravel\Softic-Era\Current Projects\PaperLess\resources\views/emails/user/reset-password.blade.php ENDPATH**/ ?>