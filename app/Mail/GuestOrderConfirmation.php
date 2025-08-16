<?php

namespace App\Mail;

use App\Models\GuestUser;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $guestUser;
    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(GuestUser $guestUser, Order $order)
    {
        $this->guestUser = $guestUser;
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order Confirmation - #{$this->order->order_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.guest.order-confirmation',
            with: [
                'guestUser' => $this->guestUser,
                'order' => $this->order,
                'orderUrl' => route('guest.orders.show', $this->order->id),
                'dashboardUrl' => route('guest.dashboard'),
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
