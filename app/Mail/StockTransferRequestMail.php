<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\StockRequest;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockTransferRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Order $order;
    public Store $sourceStore;
    public Store $destinationStore;
    public array $items;
    public int $totalQuantity;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Store $sourceStore, Store $destinationStore, array $items)
    {
        $this->order = $order;
        $this->sourceStore = $sourceStore;
        $this->destinationStore = $destinationStore;
        $this->items = $items;
        $this->totalQuantity = array_sum(array_column($items, 'quantity'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Solicitação Urgente de Transferência - Pedido #' . str_pad($this->order->id, 6, '0', STR_PAD_LEFT),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-transfer-request',
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
