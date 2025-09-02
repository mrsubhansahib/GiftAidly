<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify your email — GiftAidly')
                ->markdown('emails.user.verify-email', ['url' => $url,'user' => $notifiable]);
        });
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', ['token' => $token,'email' => $notifiable->getEmailForPasswordReset()], false));
            return (new MailMessage)
                ->subject('Reset your password — GiftAidly')
                ->markdown('emails.user.reset-password', ['url' => $url,'user' => $notifiable]);
        });
    }
}
