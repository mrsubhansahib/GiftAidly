@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

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

    $startDateFormatted = Carbon::parse($subscription->start_date)->format('d M Y');
    $endDateFormatted = Carbon::parse($subscription->end_date)->format('d M Y');

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
# 📝 New Scheduled Donation Received

A new donation subscription has been **scheduled** on GiftAidly.  
Here are the details:

---

## 👤 **Donor Information**
**Name:** {{ $user->name }}  
**Email:** {{ $user->email }}

---

## 💰 **Donation Details**
**Donation Type:** {{ $frequency }}  
**Amount:** {{ $currencySymbol }} {{ number_format($subscription->price, 2) }}  

@if ($subscription->gift_aid === 'yes')
**Gift Aid:** ✅ Applied  
@endif  

**Start Date:** {{ $startDateFormatted }}  
**End Date:** {{ $endDateFormatted }}  

@if ($subscription->gift_aid === 'yes')
---  
💡 **Gift Aid Applied**  
This donation includes Gift Aid, increasing its value by **25%**.
@endif

@else
# Dear {{ $user->name }},

Thank you for scheduling your **{{ $frequency }} donation** to **GiftAidly**.  
Your donation is set to **begin on {{ $startDateFormatted }}**.

Your support means the world to us and helps us continue our mission to make a difference ❤️.

---

## 🧾 Donation Schedule Summary

**Donation Type:** {{ $frequency }}  
**Amount:** {{ $currencySymbol }} {{ number_format($subscription->price, 2) }}  

@if ($subscription->gift_aid === 'yes')
**Gift Aid:** ✅ Applied  
@endif  

**Start Date:** {{ $startDateFormatted }}  
**End Date:** {{ $endDateFormatted }}  

@if ($subscription->gift_aid === 'yes')
---  
💡 **Gift Aid Applied**  
Thanks to Gift Aid, your donation will be worth **25% more** at no extra cost to you!
@endif

<x-mail::button :url="url('/user/donations/' . $user->reference_id)">
    View My Donations
</x-mail::button>

Thank you once again for your trust and generosity 💚  
We’re honoured to have you as part of our donor family.

Warm regards,  
**The GiftAidly Team**
@endif
</x-mail::message>
