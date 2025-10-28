@php
    use Carbon\Carbon;

    $startDate = Carbon::parse($subscription->start_date)->format('d M Y');
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
# 📥 New Zakat Donation Received

A new **Zakat** has been received on **GiftAidly**.  

---

## 👤 **Donor Information**
**Name:** {{ $user->name }}  
**Email:** {{ $user->email }}

---

## 💰 **Details**
**Amount:** {{ $currencySymbol }} {{ number_format($subscription->price, 2) }}  
**Paid At:** {{ $startDate }}

@else
# Dear {{ $user->name }},

Thank you for your generous **Zakat**.  
Your contribution will help those most in need and make a lasting impact 🌙.

---

## 🧾 Zakat Summary

**Amount:** {{ $currencySymbol }} {{ number_format($subscription->price, 2) }}  
**Paid At:** {{ $startDate }}

---

Thank you once again for your trust and generosity 💚  
We’re honoured to have you as part of our donor family.

Warm regards,  
**The GiftAidly Team**
@endif
</x-mail::message>