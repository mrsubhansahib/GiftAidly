<?php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $formattedDate = Carbon::parse($invoice->invoice_date)->format('d M Y');

    // Frequency text if invoice is linked to a subscription
    $frequency = null;
    if (isset($subscription)) {
        switch ($subscription->type) {
            case 'day':
                $frequency = 'Daily';
                break;
            case 'week':
                $frequency = 'Weekly';
                break;
            case 'month':
                $frequency = 'Monthly';
                break;
            case 'friday':
                $frequency = 'Friday';
                break;
            default:
                $frequency = Str::startsWith($subscription->type, 'special')
                    ? 'One-Time (Special)'
                    : ucfirst($subscription->type);
                break;
        }
    }
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];
    $currencyCode = strtoupper($invoice->currency);
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
# 🧾 New Invoice Paid

A new donation invoice has been **successfully paid**. Below are the details:

---

## 👤 **Donor Information**
**Name:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>  
**Email:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->email); ?>


---

## 💰 **Invoice Details**
**Amount Paid:** <?php echo new \Illuminate\Support\EncodedHtmlString($currencySymbol); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($invoice->amount_due, 2)); ?>  
**Payment Date:** <?php echo new \Illuminate\Support\EncodedHtmlString($formattedDate); ?>  
<?php if($frequency): ?>
**Donation Frequency:** <?php echo new \Illuminate\Support\EncodedHtmlString($frequency); ?>  
<?php endif; ?>
**Status:** Paid

---

<?php if(isset($subscription) && $subscription->gift_aid === 'yes'): ?>
💡 **Gift Aid Applied**  
This donation includes Gift Aid, increasing its value by **25%**.
<?php endif; ?>

<?php else: ?>
# Dear <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Thank you for your continued support to **GiftAidly**.  
We’re pleased to confirm that your **donation invoice has been successfully paid** ✅

---

## 🧾 Invoice Summary

**Amount Paid:** <?php echo new \Illuminate\Support\EncodedHtmlString($currencySymbol); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($invoice->amount_due, 2)); ?>  
**Payment Date:** <?php echo new \Illuminate\Support\EncodedHtmlString($formattedDate); ?>  
<?php if($frequency): ?>
**Donation Frequency:** <?php echo new \Illuminate\Support\EncodedHtmlString($frequency); ?>  
<?php endif; ?>
**Status:** Paid

<?php if(isset($subscription) && $subscription->gift_aid === 'yes'): ?>
---
💡 **Gift Aid Applied**  
Thanks to Gift Aid, your donation will be worth **25% more** at no extra cost to you!
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal15a5e11357468b3880ae1300c3be6c4f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15a5e11357468b3880ae1300c3be6c4f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::button'),'data' => ['url' => url('/user/invoices/index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(url('/user/invoices/index'))]); ?>
    View My Invoices
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

Thank you once again for your generosity and trust.  
Your support helps us continue making a real difference 💚

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
<?php endif; ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/email/user/invoice/paid.blade.php ENDPATH**/ ?>