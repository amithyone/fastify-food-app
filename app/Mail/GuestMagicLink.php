<?php

namespace App\Mail;

use App\Models\GuestUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestMagicLink extends Mailable
{
    use Queueable, SerializesModels;

    public $guestUser;
    public $magicLink;

    /**
     * Create a new message instance.
     */
    public function __construct(GuestUser $guestUser, string $magicLink)
    {
        $this->guestUser = $guestUser;
        $this->magicLink = $magicLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login to Your Account - AbuJaeat',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.guest.magic-link',
            with: [
                'guestUser' => $this->guestUser,
                'magicLink' => $this->magicLink,
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
