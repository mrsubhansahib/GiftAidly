<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionPaidMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $transaction;
    /**
     * Create a new message instance.
     */
    public function __construct($user, Transaction $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '💳 Transaction Paid - GiftAidly',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.transaction.paid',
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
