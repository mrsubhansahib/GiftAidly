<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $invoice;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Invoice $invoice, $isAdmin = false)
    {
        $this->user = $user;
        $this->invoice = $invoice;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? '⚠️ Invoice Payment Failed - GiftAidly'
                : '❌ Donation Invoice Failed - GiftAidly',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.user.invoice.failed',
            with: [
                'user' => $this->user,
                'invoice' => $this->invoice,
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
