@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $formattedDate = Carbon::parse($invoice->invoice_date)->format('d M Y');

    // Frequency text if invoice is linked to a subscription
    $frequency = null;
    if (isset($subscription)) {
        switch ($subscription->type) {
            case 'day': $frequency = 'Daily'; break;
            case 'week': $frequency = 'Weekly'; break;
            case 'month': $frequency = 'Monthly'; break;
            case 'friday': $frequency = 'Friday'; break;
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
@endphp

<x-mail::message>
@if($isAdmin)
# ⚠️ Invoice Payment Failed

A donation payment attempt has **failed**. Please review the details below:

---

## 👤 **Donor Information**
**Name:** {{ $user->name }}  
**Email:** {{ $user->email }}

---

## 💰 **Invoice Details**
**Amount Due:** {{ $currencySymbol }} {{ number_format($invoice->amount_due, 2) }}  
**Attempt Date:** {{ $formattedDate }}  
@if ($frequency)
**Donation Frequency:** {{ $frequency }}  
@endif
**Status:** Failed

@if(isset($subscription) && $subscription->gift_aid === 'yes')
---
💡 **Gift Aid:** This donation was Gift-Aid eligible.
@endif

@else
# Dear {{ $user->name }},

We attempted to process your recent donation payment,  
but unfortunately, the **transaction was unsuccessful** ❌

---

## 🧾 Invoice Summary

**Amount Due:** {{ $currencySymbol }} {{ number_format($invoice->amount_due, 2) }}  
**Attempt Date:** {{ $formattedDate }}  
@if ($frequency)
**Donation Frequency:** {{ $frequency }}  
@endif
**Status:** Failed

@if(isset($subscription) && $subscription->gift_aid === 'yes')
---
💡 **Gift Aid Reminder**  
Your donation is Gift-Aid eligible — once payment succeeds, it will still qualify for 25% extra at no cost to you.
@endif

<x-mail::button :url="url('/user/invoices/index')">
    Retry Payment
</x-mail::button>

We recommend checking your card details or trying another payment method.  
Thank you for your continued support 💚

Warm regards,  
**The GiftAidly Team**
@endif
</x-mail::message>
