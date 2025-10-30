<?php
    use Carbon\Carbon;

    $startDate = Carbon::parse($subscription->start_date)->format('d M Y');
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];
    $currencyCode = strtoupper($subscription->currency);
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
# 📥 New Zakat Donation Received

A new **Zakat** has been received on **GiftAidly**.  

---

## 👤 **Donor Information**
**Name:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>  
**Email:** <?php echo new \Illuminate\Support\EncodedHtmlString($user->email); ?>


---

## 💰 **Details**
**Amount:** <?php echo new \Illuminate\Support\EncodedHtmlString($currencySymbol); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($subscription->price, 2)); ?>  
**Paid At:** <?php echo new \Illuminate\Support\EncodedHtmlString($startDate); ?>


<?php else: ?>
# Dear <?php echo new \Illuminate\Support\EncodedHtmlString($user->name); ?>,

Thank you for your generous **Zakat**.  
Your contribution will help those most in need and make a lasting impact 🌙.

---

## 🧾 Zakat Summary

**Amount:** <?php echo new \Illuminate\Support\EncodedHtmlString($currencySymbol); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($subscription->price, 2)); ?>  
**Paid At:** <?php echo new \Illuminate\Support\EncodedHtmlString($startDate); ?>


---

Thank you once again for your trust and generosity 💚  
We’re honoured to have you as part of our donor family.

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
<?php endif; ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/email/user/zakat.blade.php ENDPATH**/ ?>