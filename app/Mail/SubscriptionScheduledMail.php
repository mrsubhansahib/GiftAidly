<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Subscription $subscription, $isAdmin = false)
    {
        $this->user = $user;
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
                ? '📝 New Scheduled Donation - GiftAidly'
                : '🎯 Your Donation Has Been Scheduled',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.subscrption.scheduled',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'isAdmin' => $this->isAdmin,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
