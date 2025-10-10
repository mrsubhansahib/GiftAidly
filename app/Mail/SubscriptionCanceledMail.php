<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SubscriptionCanceledMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subscription;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct($subscription, $isAdmin = false)
    {
        $this->subscription = $subscription;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? '⚠️ Donation Canceled - GiftAidly'
                : '❌ Your Donation Has Been Canceled - GiftAidly',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.subscrption.canceled',
            with: [
                'subscription' => $this->subscription,
                'isAdmin' => $this->isAdmin,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
