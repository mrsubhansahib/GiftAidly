<x-mail::message>
# Welcome to {{ config('app.name') }}, {{ $user->name }} 🎉

We’re excited to have you on board!  
Your account has been successfully created and verified. You’re now part of our growing community.

<x-mail::button :url="route('signin')">
Sign in
</x-mail::button>

Thanks for joining us. We look forward to building something great together.

Warm regards,  
**The {{ config('app.name') }} Team**
</x-mail::message>
