@php
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
@endphp

<x-mail::message>
# Dear {{ $user->name }},

Thank you for your continued support to **GiftAidly**.  
We’re pleased to confirm that your **donation invoice has been successfully paid** ✅

---

## 🧾 Invoice Summary

**Amount Paid:** {{ strtoupper($invoice->currency) }} {{ number_format($invoice->amount_due, 2) }}  
**Payment Date:** {{ $formattedDate }}  
@if ($frequency)
**Donation Frequency:** {{ $frequency }}  
@endif
**Status:** Paid

---

<x-mail::button :url="url('/user/invoices/index')">
    View My Invoices
</x-mail::button>

Thank you once again for your generosity and trust.  
Your support helps us continue making a real difference 💚

Warm regards,  
**The GiftAidly Team**
</x-mail::message>
