<x-mail::message>
# Welcome to {{ config('app.name') }}, {{ $user->name }} 🎉

We’re excited to have you on board!  
Your account has been successfully created, and you’re now part of a growing community that values trust, innovation, and connection.

**Here’s what you can do next:**
- Explore your dashboard and update your profile  
- Access resources and tools designed to help you succeed  
- Stay tuned for updates, tips, and opportunities from our team  

<x-mail::button :url="route('signin')">
Sign in 
</x-mail::button>

If you have any questions or need assistance, our support team is here to help — just reply to this email or visit our Help Center.

Thanks for joining us, and welcome once again to **{{ config('app.name') }}**.  
We look forward to building something great together.

Warm regards,  
**The {{ config('app.name') }} Team**

<p style="font-size:12px; color:#6c757d;">
    If you did not create this account, please ignore this email or contact our support team immediately.
</p>
</x-mail::message>
