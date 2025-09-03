<x-mail::message>
# Reset your password 🔐

Hi {{ $user->name ?? 'there' }},  
We received a request to reset your password for **{{ config('app.name') }}**.

<x-mail::button :url="$url">
Reset Password
</x-mail::button>

This link expires in **60 minutes**. If you didn’t request a reset, no action is required and you can ignore this email.

Stay secure,  
**The {{ config('app.name') }} Team**

<x-mail::subcopy>
If the button above doesn’t work, copy and paste this URL into your browser:  
{{ $url }}
</x-mail::subcopy>
</x-mail::message>
