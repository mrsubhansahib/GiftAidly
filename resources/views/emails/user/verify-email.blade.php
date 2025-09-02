@component('mail::message')
{{-- Logo / Header --}}
{{-- <div style="text-align:center;margin-bottom:16px;">
  <img src="{{ url('/images/logo-dark.png') }}" alt="GiftAidly" height="32">
</div> --}}

# Verify your email

Hi {{ $user->name ?? 'there' }},  
Thanks for signing up. Please confirm your email to activate your account.

@component('mail::button', ['url' => $url])
Verify Email
@endcomponent

This link will expire in 60 minutes. If you didn’t create an account, you can safely ignore this email.

Warm regards,  
**The {{ config('app.name') }} Team**
@endcomponent
