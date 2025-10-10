@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

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

    $startDate = Carbon::parse($subscription->start_date)->format('d M Y');
    $endDate = Carbon::parse($subscription->end_date)->format('d M Y');
    $currencySymbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];
    $currencyCode = strtoupper($subscription->currency);
    $currencySymbol = $currencySymbols[$currencyCode] ?? $currencyCode;
@endphp

<x-mail::message>
@if($isAdmin)
# ⚠️ Donation Canceled

A donation subscription has been **canceled** on **GiftAidly**. Below are the details:

---

## 👤 **Donor Information**
**Name:** {{ $subscription->user->name }}  
**Email:** {{ $subscription->user->email }}

---

## 💸 **Donation Details**
**Donation Type:** {{ $frequency }}  
**Amount:** {{ $currencySymbol }} {{ number_format($subscription->price, 2) }}  
**Frequency:** {{ $frequency }}  
**Gift Aid:** {{ $subscription->gift_aid === 'yes' ? '✅ Applied' : '❌ Not Applied' }}

@if (Str::startsWith($subscription->type, 'special'))
**Donated At:** {{ $startDate }}
@else
**Start Date:** {{ $startDate }}  
**End Date:** {{ $endDate }}  
**Status:** {{ ucfirst($subscription->status) }}
@endif

---

📬 This cancellation notice helps you keep donation records up to date.

@else
# Dear {{ $subscription->user->name }},

We’re sorry to inform you that your **{{ $frequency }} donation** has been **canceled**.

We truly appreciate your previous support — your generosity made a real impact 💚.

---

## 🧾 Donation Summary

**Donation Type:** {{ $frequency }}  
**Amount:** {{ $currencySymbol }} {{ number_format($subscription->price, 2) }}  
**Frequency:** {{ $frequency }}  
**Gift Aid:** {{ $subscription->gift_aid === 'yes' ? '✅ Applied' : '❌ Not Applied' }}  
@if (Str::startsWith($subscription->type, 'special'))
**Donated At:** {{ $startDate }}
@else
**Start Date:** {{ $startDate }}  
**End Date:** {{ $endDate }}  
**Status:** {{ ucfirst($subscription->status) }}
@endif

@if ($subscription->gift_aid === 'yes')
---
💡 **Gift Aid Applied**  
Your past donations included Gift Aid, increasing their value by **25%**.
@endif

<x-mail::button :url="url('/user/donations/index')">
    View My Donations
</x-mail::button>

We hope you’ll consider supporting us again in the future 🙏  
Together, we can continue making a positive difference 🌍

Warm regards,  
**The GiftAidly Team**
@endif
</x-mail::message>
