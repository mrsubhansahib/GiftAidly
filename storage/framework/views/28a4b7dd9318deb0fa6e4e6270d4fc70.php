<?php
    use Carbon\Carbon;

    $formattedDate = Carbon::parse($transaction->paid_at)->format('d M Y H:i');

    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];
    $currencyCode = strtoupper($transaction->invoice->currency);
    $currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;
?>

<?php if (isset($component)) { $__componentOriginalaa758e6a82983efcbf593f765e026bd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa758e6a82983efcbf593f765e026bd9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::message'),'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php if($isAdmin): ?>
# 🧾 New Transaction Paid

A new transaction has been **successfully processed**. Below are the details:

---

## 👤 **Donor Information**
**Name:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>  
**Email:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->email); ?>


---

## 💳 **Transaction Details**
**Amount:** <?php echo new \Illuminate\Support\EncodedHtmlString($currencySymbol); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($transaction->invoice->amount_due, 2)); ?>  
**Date:** <?php echo new \Illuminate\Support\EncodedHtmlString($formattedDate); ?>  
**Status:** <?php echo new \Illuminate\Support\EncodedHtmlString(ucfirst($transaction->status)); ?>


<?php if(isset($transaction->invoice->subscription) && $transaction->invoice->subscription->gift_aid === 'yes'): ?>
---
💡 **Gift Aid Applied**  
This donation includes Gift Aid, increasing its value by **25%**.
<?php endif; ?>

<?php else: ?>
# Dear <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Thank you for your generous support to **GiftAidly**.  
Your transaction has been **successfully processed** ✅

---

## 💳 Transaction Details

**Amount:** <?php echo new \Illuminate\Support\EncodedHtmlString($currencySymbol); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($transaction->invoice->amount_due, 2)); ?>  
**Date:** <?php echo new \Illuminate\Support\EncodedHtmlString($formattedDate); ?>  
**Status:** <?php echo new \Illuminate\Support\EncodedHtmlString(ucfirst($transaction->status)); ?>


<?php if(isset($transaction->invoice->subscription) && $transaction->invoice->subscription->gift_aid === 'yes'): ?>
---
💡 **Gift Aid Applied**  
Thanks to Gift Aid, your donation will be worth **25% more** at no extra cost to you!
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal15a5e11357468b3880ae1300c3be6c4f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::button'),'data' => ['url' => url('/user/donations/index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(url('/user/donations/index'))]); ?>
    View My Donations
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal15a5e11357468b3880ae1300c3be6c4f)): ?>
<?php $attributes = $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f; ?>
<?php unset($__attributesOriginal15a5e11357468b3880ae1300c3be6c4f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal15a5e11357468b3880ae1300c3be6c4f)): ?>
<?php $component = $__componentOriginal15a5e11357468b3880ae1300c3be6c4f; ?>
<?php unset($__componentOriginal15a5e11357468b3880ae1300c3be6c4f); ?>
<?php endif; ?>

Thank you again for your generosity 💚

Warm regards,  
**The GiftAidly Team**
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $attributes = $__attributesOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $component = $__componentOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__componentOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/email/user/transaction/paid.blade.php ENDPATH**/ ?>