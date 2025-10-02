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
@endphp

<x-mail::message>
# Dear {{ $user->name }},

Thank you for your generous **{{ $frequency }} donation** to **GiftAidly**.  
Your support means the world to us and helps us continue our mission to make a difference ❤️.

---

## 🧾 Donation Summary

**Donation Type:** {{ $frequency }}  
**Amount:** {{ strtoupper($subscription->currency) }} {{ number_format($subscription->price, 2) }}  
**Frequency:** {{ $frequency }}  
**Gift Aid:** {{ $subscription->gift_aid === 'yes' ? '✅ Applied' : '❌ Not Applied' }}  
**Donated at:** {{ $startDate }}  

@if ($subscription->gift_aid === 'yes')
---
💡 **Gift Aid Applied**  
Thanks to Gift Aid, your donation will be worth **25% more** at no extra cost to you!
@endif

<x-mail::button :url="url('/user/donations/index')">
    View My Donations
</x-mail::button>

Thank you once again for your trust and generosity.  
We’re honoured to have you as part of our donor family 💚

Warm regards,  
**The GiftAidly Team**
</x-mail::message>
