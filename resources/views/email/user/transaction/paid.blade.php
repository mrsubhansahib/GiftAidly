@php
    use Carbon\Carbon;

    $formattedDate = Carbon::parse($transaction->paid_at)->format('d M Y H:i');
@endphp

<x-mail::message>
@if($isAdmin)
# 🧾 New Transaction Paid

A new transaction has been **successfully processed**. Below are the details:

---

## 👤 **Donor Information**
**Name:** {{ $user->name }}  
**Email:** {{ $user->email }}

---

## 💳 **Transaction Details**
**Amount:** {{ strtoupper($transaction->invoice->currency) }} {{ number_format($transaction->invoice->amount_due, 2) }}  
**Date:** {{ $formattedDate }}  
**Status:** {{ ucfirst($transaction->status) }}

@if(isset($transaction->invoice->subscription) && $transaction->invoice->subscription->gift_aid === 'yes')
---
💡 **Gift Aid Applied**  
This donation includes Gift Aid, increasing its value by **25%**.
@endif

@else
# Dear {{ $user->name }},

Thank you for your generous support to **GiftAidly**.  
Your transaction has been **successfully processed** ✅

---

## 💳 Transaction Details

**Amount:** {{ strtoupper($transaction->invoice->currency) }} {{ number_format($transaction->invoice->amount_due, 2) }}  
**Date:** {{ $formattedDate }}  
**Status:** {{ ucfirst($transaction->status) }}

@if(isset($transaction->invoice->subscription) && $transaction->invoice->subscription->gift_aid === 'yes')
---
💡 **Gift Aid Applied**  
Thanks to Gift Aid, your donation will be worth **25% more** at no extra cost to you!
@endif

<x-mail::button :url="url('/user/donations/index')">
    View My Donations
</x-mail::button>

Thank you again for your generosity 💚

Warm regards,  
**The GiftAidly Team**
@endif
</x-mail::message>
