<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZakatMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $isAdmin;

    public function __construct($user, $subscription, $isAdmin = false)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->isAdmin = $isAdmin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? '📥 New Zakat Received!'
                : '🌙 Thank You for Your Zakat!'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.zakat',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'isAdmin' => $this->isAdmin,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
