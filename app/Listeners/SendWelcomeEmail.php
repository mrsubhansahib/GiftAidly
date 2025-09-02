<?php

namespace App\Listeners;

use App\Mail\WelcomeUserMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
            // Log::info('SendWelcomeEmail fired for: '.$event->user->email);

        try {
            Mail::to($event->user->email)->queue(new WelcomeUserMail($event->user));
        } catch (\Exception $e) {
            report($e); // log for you
            // Optional: put a soft warning in session for the next request
            session()->flash('mail_warning', 'We could not send your welcome email. Please check your address.');
        }
    }
}
