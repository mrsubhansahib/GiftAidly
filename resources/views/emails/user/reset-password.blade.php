@component('mail::message')
{{-- <div style="text-align:center;margin-bottom:16px;">
  <img src="{{ url('/images/logo-dark.png') }}" alt="GiftAidly" height="32">
</div> --}}

# Reset your password

Hi {{ $user->name ?? 'there' }},  
We received a request to reset your password.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

This link expires in 60 minutes. If you didn’t request a reset, no action is required.

Stay secure,<br>
**The {{ config('app.name') }} Team**
@endcomponent
