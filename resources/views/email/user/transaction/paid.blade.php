@php
    use Carbon\Carbon;
    $formattedDate = Carbon::parse($transaction->paid_at)->format('d M Y H:i');
@endphp

<x-mail::message>
# Dear {{ $user->name }},

Thank you for your generous support to **GiftAidly**.  
Your transaction has been **successfully processed** ✅

---

## 💳 Transaction Details

**Amount:** {{ strtoupper($transaction->invoice->currency) }} {{ number_format($transaction->invoice->amount_due, 2) }}  
**Date:** {{ $formattedDate }}  
**Status:** {{ ucfirst($transaction->status) }}

---

<x-mail::button :url="url('/user/donations/index')">
    View My Donations
</x-mail::button>

Thank you again for your generosity 💚

Warm regards,  
**The GiftAidly Team**
</x-mail::message>
