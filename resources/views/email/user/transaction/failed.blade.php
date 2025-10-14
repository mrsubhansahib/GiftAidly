@php
    use Carbon\Carbon;

    $formattedDate = Carbon::parse($transaction->paid_at ?? now())->format('d M Y H:i');

    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];
    $currencyCode = strtoupper($transaction->invoice->currency ?? 'GBP');
    $currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;
@endphp

<x-mail::message>
@if($isAdmin)
# ⚠️ Transaction Failed

A transaction has **failed** during processing. Below are the details:

---

## 👤 **Donor Information**
**Name:** {{ $user->name }}  
**Email:** {{ $user->email }}

---

## 💳 **Transaction Details**
**Amount:** {{ $currencySymbol }} {{ number_format($transaction->invoice->amount_due, 2) }}  
**Date:** {{ $formattedDate }}  
**Status:** {{ ucfirst($transaction->status) }}

@if(isset($transaction->invoice->subscription) && $transaction->invoice->subscription->gift_aid === 'yes')
---
💡 **Gift Aid Applied**  
This donation includes Gift Aid eligibility.
@endif

@else
# Dear {{ $user->name }},

We attempted to process your recent donation,  
but unfortunately the **transaction failed** ❌

---

## 💳 Transaction Details

**Amount:** {{ $currencySymbol }} {{ number_format($transaction->invoice->amount_due, 2) }}  
**Date:** {{ $formattedDate }}  
**Status:** {{ ucfirst($transaction->status) }}

@if(isset($transaction->invoice->subscription) && $transaction->invoice->subscription->gift_aid === 'yes')
---
💡 **Gift Aid Reminder**  
Your donation qualifies for Gift Aid — once payment succeeds, it will still gain 25% extra value at no cost to you.
@endif

<x-mail::button :url="url('/user/donations/index')">
    Retry Donation
</x-mail::button>

We recommend checking your card details or using another payment method.  
Thank you for your continued support 💚

Warm regards,  
**The GiftAidly Team**
@endif
</x-mail::message>
