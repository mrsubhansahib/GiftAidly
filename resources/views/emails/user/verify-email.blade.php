<x-mail::message>
# Verify your email, {{ $user->name ?? 'there' }} ✅

Thanks for signing up to **{{ config('app.name') }}**.  
Please confirm your email to activate your account and start using the platform.

<x-mail::button :url="$url">
Verify Email
</x-mail::button>

This link will expire in **60 minutes** for security.

If you didn’t create this account, you can safely ignore this email.

Warm regards,  
**The {{ config('app.name') }} Team**

<x-mail::subcopy>
If the button above doesn’t work, copy and paste this URL into your browser:  
{{ $url }}
</x-mail::subcopy>
</x-mail::message>
