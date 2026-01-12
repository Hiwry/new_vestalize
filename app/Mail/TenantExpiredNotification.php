<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantExpiredNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tenant;
    public $type; // 'trial' or 'subscription'

    /**
     * Create a new message instance.
     */
    public function __construct($tenant, $type = 'assinatura')
    {
        $this->tenant = $tenant;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Vencimento de ' . ucfirst($this->type) . ': ' . $this->tenant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.tenant-expired',
            with: [
                'tenant' => $this->tenant,
                'type' => $this->type,
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
