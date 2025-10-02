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
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Transaction $transaction, $isAdmin = false)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? '🧾 New Transaction Paid - GiftAidly'
                : '💳 Transaction Paid - GiftAidly',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.transaction.paid',
            with: [
                'user' => $this->user,
                'transaction' => $this->transaction,
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
